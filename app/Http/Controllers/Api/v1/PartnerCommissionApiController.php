<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\PartnerCommissionResource;
use App\Http\Resources\v1\PartnerCommissionSettlementResource;
use App\Models\AccountingPeriod;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\OfficeAccount;
use App\Models\PartnerCommission;
use App\Models\PartnerCommissionSettlement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerCommissionApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $query = PartnerCommission::with([
            'student',
            'university',
            'partnerAgency',
            'country',
            'handoverOfficer',
        ]);

        if ($request->filled('type') && in_array($request->type, ['university', 'agency'])) {
            $query->where('commission_type', $request->type);
        }

        if ($request->filled('direction') && in_array($request->direction, ['receivable', 'payable'])) {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('workflow_status')) {
            $query->where('workflow_status', $request->workflow_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

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

        $commissions = $query->latest('id')->paginate($request->input('per_page', 15));

        // Calculate summary metrics
        $metrics = [
            'total_university_receivable' => (float) PartnerCommission::where('commission_type', 'university')->where('direction', 'receivable')->sum('total_amount_bdt'),
            'total_university_received' => (float) PartnerCommission::where('commission_type', 'university')->where('direction', 'receivable')->sum('paid_amount_bdt'),
            'total_agency_payable' => (float) PartnerCommission::where('commission_type', 'agency')->where('direction', 'payable')->sum('total_amount_bdt'),
            'total_agency_paid' => (float) PartnerCommission::where('commission_type', 'agency')->where('direction', 'payable')->sum('paid_amount_bdt'),
            'total_receivable_due' => (float) PartnerCommission::where('direction', 'receivable')->sum('due_amount_bdt'),
            'total_payable_due' => (float) PartnerCommission::where('direction', 'payable')->sum('due_amount_bdt'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Partner commissions retrieved successfully.',
            'data' => PartnerCommissionResource::collection($commissions->items()),
            'metrics' => $metrics,
            'meta' => [
                'current_page' => $commissions->currentPage(),
                'last_page' => $commissions->lastPage(),
                'per_page' => $commissions->perPage(),
                'total' => $commissions->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
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
        $validated['paid_amount_bdt'] = 0.00;
        $validated['due_amount_bdt'] = $totalAmountBdt;
        $validated['payment_status'] = 'unpaid';
        $validated['created_by'] = Auth::id();

        $commission = PartnerCommission::create($validated);
        $commission->load(['student', 'university', 'partnerAgency', 'country', 'handoverOfficer']);

        return $this->sendResponse(
            new PartnerCommissionResource($commission),
            'Partner commission deal created successfully.',
            201
        );
    }

    public function show(PartnerCommission $partnerCommission): JsonResponse
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $partnerCommission->load([
            'student.applications.university',
            'university.country',
            'partnerAgency',
            'country',
            'handoverOfficer',
            'settlements.officeAccount',
            'settlements.creator',
        ]);

        return $this->sendResponse(
            new PartnerCommissionResource($partnerCommission),
            'Partner commission details retrieved successfully.'
        );
    }

    public function update(Request $request, PartnerCommission $partnerCommission): JsonResponse
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
        $partnerCommission->load(['student', 'university', 'partnerAgency', 'country', 'handoverOfficer']);

        return $this->sendResponse(
            new PartnerCommissionResource($partnerCommission),
            'Partner commission deal updated successfully.'
        );
    }

    public function settle(Request $request, PartnerCommission $partnerCommission): JsonResponse
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

            // Double-entry ledger posting
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
                    // Debit Cash/Bank Asset, Credit Revenue
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
                    // Debit Expense, Credit Cash/Bank Asset
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

            // Create settlement record
            $settlement = PartnerCommissionSettlement::create([
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

            $partnerCommission->recalculateBalances();

            if ($partnerCommission->due_amount_bdt <= 0 && $partnerCommission->workflow_status === 'processing') {
                $partnerCommission->update(['workflow_status' => 'settled']);
            }

            DB::commit();

            $partnerCommission->load(['settlements.officeAccount', 'settlements.creator']);

            return $this->sendResponse([
                'commission' => new PartnerCommissionResource($partnerCommission),
                'settlement' => new PartnerCommissionSettlementResource($settlement),
            ], 'Settlement recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Settlement failed: ' . $e->getMessage(), [], 500);
        }
    }

    public function destroy(PartnerCommission $partnerCommission): JsonResponse
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $partnerCommission->delete();

        return $this->sendResponse(null, 'Partner commission deal deleted successfully.');
    }
}
