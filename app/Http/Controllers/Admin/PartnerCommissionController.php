<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingPeriod;
use App\Models\ChartOfAccount;
use App\Models\Country;
use App\Models\Currency;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\OfficeAccount;
use App\Models\PartnerAgency;
use App\Models\PartnerCommission;
use App\Models\PartnerCommissionSettlement;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerCommissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $query = PartnerCommission::with([
            'student',
            'university',
            'partnerAgency',
            'country',
            'handoverOfficer',
            'settlements.officeAccount'
        ]);

        // Filter: Commission Type (university or agency)
        if ($request->filled('type') && in_array($request->type, ['university', 'agency'])) {
            $query->where('commission_type', $request->type);
        }

        // Filter: Direction (receivable or payable)
        if ($request->filled('direction') && in_array($request->direction, ['receivable', 'payable'])) {
            $query->where('direction', $request->direction);
        }

        // Filter: Workflow Status
        if ($request->filled('workflow_status')) {
            $query->where('workflow_status', $request->workflow_status);
        }

        // Filter: Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter: Search Keyword (student name, passport, agency name, university name)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('passport_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('partnerAgency', function ($aq) use ($search) {
                    $aq->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%");
                })
                ->orWhereHas('university', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('agreement_reference', 'like', "%{$search}%");
            });
        }

        $commissions = $query->latest()->paginate(15)->withQueryString();

        // Summary Metrics
        $totalUniversityReceivable = PartnerCommission::where('commission_type', 'university')
            ->where('direction', 'receivable')
            ->sum('total_amount_bdt');

        $totalUniversityReceived = PartnerCommission::where('commission_type', 'university')
            ->where('direction', 'receivable')
            ->sum('paid_amount_bdt');

        $totalAgencyPayable = PartnerCommission::where('commission_type', 'agency')
            ->where('direction', 'payable')
            ->sum('total_amount_bdt');

        $totalAgencyPaid = PartnerCommission::where('commission_type', 'agency')
            ->where('direction', 'payable')
            ->sum('paid_amount_bdt');

        $totalReceivableDue = PartnerCommission::where('direction', 'receivable')->sum('due_amount_bdt');
        $totalPayableDue = PartnerCommission::where('direction', 'payable')->sum('due_amount_bdt');

        $agencies = PartnerAgency::withCount('commissions')->orderBy('name')->get();
        $officeAccounts = OfficeAccount::where('status', 'active')->orderBy('account_name')->get();

        return view('admin.partner-commissions.index', compact(
            'commissions',
            'totalUniversityReceivable',
            'totalUniversityReceived',
            'totalAgencyPayable',
            'totalAgencyPaid',
            'totalReceivableDue',
            'totalPayableDue',
            'agencies',
            'officeAccounts'
        ));
    }

    public function create()
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $students = Student::with(['country', 'university', 'applications'])
            ->select('id', 'first_name', 'last_name', 'passport_number', 'phone', 'country_id', 'university_id')
            ->latest('id')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                    'passport' => $s->passport_number ?? '',
                    'phone' => $s->phone ?? '',
                    'country_id' => $s->country_id,
                    'country_name' => $s->country->name ?? '',
                    'university_id' => $s->university_id,
                    'university_name' => $s->university->name ?? '',
                    'applications' => $s->applications->map(function ($app) {
                        return [
                            'id' => $app->id,
                            'tuition_fee' => $app->tuition_fee ?? 0,
                            'status' => $app->status ?? 'active',
                            'visa_status' => $app->visa_status ?? 'pending',
                        ];
                    })
                ];
            });

        $agencies = PartnerAgency::active()->orderBy('name')->get();
        $universities = University::whereIn('status', [1, '1', 'active'])->orderBy('name')->get();
        $countries = Country::whereIn('status', [1, '1', 'active'])->orderBy('name')->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        $currencies = Currency::active()->get();

        return view('admin.partner-commissions.create', compact(
            'students',
            'agencies',
            'universities',
            'countries',
            'users',
            'currencies'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $validated = $request->validate([
            'commission_type' => 'required|in:university,agency',
            'direction' => 'required|in:receivable,payable',
            'agency_referral_type' => 'nullable|in:outbound_handover,inbound_referral',
            'student_id' => 'required|exists:students,id',
            'university_id' => 'nullable|exists:universities,id',
            'partner_agency_id' => 'nullable|exists:partner_agencies,id',
            'country_id' => 'nullable|exists:countries,id',
            'application_id' => 'nullable|exists:applications,id',
            'handover_officer_id' => 'nullable|exists:users,id',
            'rate_type' => 'required|in:percentage,fixed',
            'rate_value' => 'required|numeric|min:0',
            'base_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'workflow_status' => 'required|in:pending,processing,visa_approved,settled,cancelled',
            'handover_date' => 'nullable|date',
            'processing_stage' => 'nullable|string|max:100',
            'agreement_reference' => 'nullable|string|max:100',
            'processing_notes' => 'nullable|string',
        ]);

        // Calculate commission
        $rateValue = (float) $validated['rate_value'];
        $baseAmount = (float) ($validated['base_amount'] ?? 0);
        $exchangeRate = (float) $validated['exchange_rate'];

        if ($validated['rate_type'] === 'percentage') {
            $totalCommission = round(($baseAmount * $rateValue) / 100, 2);
        } else {
            $totalCommission = round($rateValue, 2);
        }

        $totalAmountBdt = round($totalCommission * $exchangeRate, 2);

        $validated['total_commission'] = $totalCommission;
        $validated['total_amount_bdt'] = $totalAmountBdt;
        $validated['paid_amount_bdt'] = 0.00;
        $validated['due_amount_bdt'] = $totalAmountBdt;
        $validated['payment_status'] = 'unpaid';
        $validated['created_by'] = Auth::id();

        $commission = PartnerCommission::create($validated);

        return redirect()
            ->route('admin.partner-commissions.show', $commission)
            ->with('success', 'Partner commission record created successfully.');
    }

    public function show(PartnerCommission $partnerCommission)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $partnerCommission->load([
            'student.applications.university',
            'student.country',
            'student.university',
            'university.country',
            'partnerAgency',
            'country',
            'handoverOfficer',
            'creator',
            'settlements.officeAccount',
            'settlements.creator',
        ]);

        $officeAccounts = OfficeAccount::where('status', 'active')->orderBy('account_name')->get();

        return view('admin.partner-commissions.show', compact('partnerCommission', 'officeAccounts'));
    }

    public function edit(PartnerCommission $partnerCommission)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $students = Student::with(['country', 'university', 'applications'])
            ->select('id', 'first_name', 'last_name', 'passport_number', 'phone', 'country_id', 'university_id')
            ->latest('id')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                    'passport' => $s->passport_number ?? '',
                    'phone' => $s->phone ?? '',
                    'country_id' => $s->country_id,
                    'country_name' => $s->country->name ?? '',
                    'university_id' => $s->university_id,
                    'university_name' => $s->university->name ?? '',
                    'applications' => $s->applications->map(function ($app) {
                        return [
                            'id' => $app->id,
                            'tuition_fee' => $app->tuition_fee ?? 0,
                            'status' => $app->status ?? 'active',
                            'visa_status' => $app->visa_status ?? 'pending',
                        ];
                    })
                ];
            });

        $agencies = PartnerAgency::active()->orderBy('name')->get();
        $universities = University::whereIn('status', [1, '1', 'active'])->orderBy('name')->get();
        $countries = Country::whereIn('status', [1, '1', 'active'])->orderBy('name')->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        $currencies = Currency::active()->get();

        return view('admin.partner-commissions.edit', compact(
            'partnerCommission',
            'students',
            'agencies',
            'universities',
            'countries',
            'users',
            'currencies'
        ));
    }

    public function update(Request $request, PartnerCommission $partnerCommission)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $validated = $request->validate([
            'commission_type' => 'required|in:university,agency',
            'direction' => 'required|in:receivable,payable',
            'agency_referral_type' => 'nullable|in:outbound_handover,inbound_referral',
            'student_id' => 'required|exists:students,id',
            'university_id' => 'nullable|exists:universities,id',
            'partner_agency_id' => 'nullable|exists:partner_agencies,id',
            'country_id' => 'nullable|exists:countries,id',
            'application_id' => 'nullable|exists:applications,id',
            'handover_officer_id' => 'nullable|exists:users,id',
            'rate_type' => 'required|in:percentage,fixed',
            'rate_value' => 'required|numeric|min:0',
            'base_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'workflow_status' => 'required|in:pending,processing,visa_approved,settled,cancelled',
            'handover_date' => 'nullable|date',
            'processing_stage' => 'nullable|string|max:100',
            'agreement_reference' => 'nullable|string|max:100',
            'processing_notes' => 'nullable|string',
        ]);

        $rateValue = (float) $validated['rate_value'];
        $baseAmount = (float) ($validated['base_amount'] ?? 0);
        $exchangeRate = (float) $validated['exchange_rate'];

        if ($validated['rate_type'] === 'percentage') {
            $totalCommission = round(($baseAmount * $rateValue) / 100, 2);
        } else {
            $totalCommission = round($rateValue, 2);
        }

        $totalAmountBdt = round($totalCommission * $exchangeRate, 2);

        $validated['total_commission'] = $totalCommission;
        $validated['total_amount_bdt'] = $totalAmountBdt;

        $partnerCommission->update($validated);
        $partnerCommission->recalculateBalances();

        return redirect()
            ->route('admin.partner-commissions.show', $partnerCommission)
            ->with('success', 'Partner commission updated successfully.');
    }

    public function settle(Request $request, PartnerCommission $partnerCommission)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $validated = $request->validate([
            'office_account_id' => 'required|exists:office_accounts,id',
            'amount_bdt' => 'required|numeric|min:1|max:' . max(1, (float)$partnerCommission->due_amount_bdt),
            'payment_method' => 'required|string|max:50',
            'settlement_date' => 'required|date',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $officeAccount = OfficeAccount::findOrFail($validated['office_account_id']);
            $period = AccountingPeriod::where(function ($q) {
                $q->where('status', 'open')
                  ->orWhere('is_closed', false)
                  ->orWhereNull('is_closed');
            })
            ->orderBy('start_date', 'desc')
            ->first();

            $settlementType = $partnerCommission->direction === 'receivable' ? 'received' : 'paid';
            $studentName = $partnerCommission->student ? trim($partnerCommission->student->first_name . ' ' . $partnerCommission->student->last_name) : 'Student #' . $partnerCommission->student_id;

            $journalEntryId = null;

            // Double-entry ledger posting if period and chart of account exist
            if ($officeAccount->chart_of_account_id && $period) {
                $refCode = 'JV-PCOM-' . $partnerCommission->id . '-' . time();
                $entry = JournalEntry::create([
                    'period_id' => $period->id,
                    'date' => $validated['settlement_date'],
                    'reference_number' => $refCode,
                    'note' => ucfirst($settlementType) . " Partner Commission for {$studentName} (" . ucfirst($partnerCommission->commission_type) . ")",
                    'status' => 'posted',
                    'created_by' => Auth::id(),
                ]);

                $journalEntryId = $entry->id;

                if ($settlementType === 'received') {
                    // Incoming commission: Debit Cash/Bank Asset, Credit Revenue Head
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $officeAccount->chart_of_account_id,
                        'debit' => $validated['amount_bdt'],
                        'credit' => 0,
                        'description' => "Partner Commission received in {$officeAccount->account_name} for {$studentName}",
                    ]);

                    $incomeHead = ChartOfAccount::where('code', '41002')->first() 
                        ?? ChartOfAccount::where('type', 'revenue')->first();

                    if ($incomeHead) {
                        JournalEntryItem::create([
                            'journal_entry_id' => $entry->id,
                            'chart_of_account_id' => $incomeHead->id,
                            'debit' => 0,
                            'credit' => $validated['amount_bdt'],
                            'description' => "Commission Income from " . ($partnerCommission->university->name ?? $partnerCommission->partnerAgency->name ?? 'Partner'),
                        ]);
                    }
                } else {
                    // Outgoing commission to agency: Debit Expense Head, Credit Cash/Bank Asset
                    $expenseHead = ChartOfAccount::where('code', '51007')->first() 
                        ?? ChartOfAccount::where('type', 'expense')->first();

                    if ($expenseHead) {
                        JournalEntryItem::create([
                            'journal_entry_id' => $entry->id,
                            'chart_of_account_id' => $expenseHead->id,
                            'debit' => $validated['amount_bdt'],
                            'credit' => 0,
                            'description' => "Agency Referral Expense paid to " . ($partnerCommission->partnerAgency->name ?? 'Partner Agency') . " for {$studentName}",
                        ]);
                    }

                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $officeAccount->chart_of_account_id,
                        'debit' => 0,
                        'credit' => $validated['amount_bdt'],
                        'description' => "Agency commission payment deducted from {$officeAccount->account_name}",
                    ]);
                }
            }

            // Create Settlement record
            PartnerCommissionSettlement::create([
                'partner_commission_id' => $partnerCommission->id,
                'office_account_id' => $officeAccount->id,
                'journal_entry_id' => $journalEntryId,
                'settlement_type' => $settlementType,
                'amount_bdt' => $validated['amount_bdt'],
                'payment_method' => $validated['payment_method'],
                'reference_no' => $validated['reference_no'] ?? null,
                'settlement_date' => $validated['settlement_date'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Recalculate commission balances
            $partnerCommission->recalculateBalances();

            if ($partnerCommission->due_amount_bdt <= 0 && $partnerCommission->workflow_status === 'processing') {
                $partnerCommission->update(['workflow_status' => 'settled']);
            }

            DB::commit();

            return redirect()
                ->route('admin.partner-commissions.show', $partnerCommission)
                ->with('success', 'Commission settlement recorded successfully and ledger updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors(['msg' => 'Settlement failed: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(PartnerCommission $partnerCommission)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $partnerCommission->delete();

        return redirect()
            ->route('admin.partner-commissions.index')
            ->with('success', 'Partner commission record deleted successfully.');
    }
}
