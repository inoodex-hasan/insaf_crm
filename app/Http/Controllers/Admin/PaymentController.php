<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\{Application, Invoice, OfficeAccount, Payment, Setting, Student, User, JournalEntry, JournalEntryItem, AccountingPeriod, ChartOfAccount};
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('*accountant');

        $query = Payment::with(['student', 'application', 'collector']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'student',
                    function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    }
                )->orWhere('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas(
                        'application',
                        function ($aq) use ($search) {
                            $aq->where('application_id', 'like', "%{$search}%");
                        }
                    );
            });
        }

        if ($type = $request->get('payment_type')) {
            $query->where('payment_type', $type);
        }

        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();
        $totalCount = Payment::count();
        $completedCount = Payment::where('payment_status', 'completed')->count();

        return view('admin.payments.index', compact('payments', 'totalCount', 'completedCount'));
    }

    public function create(Request $request)
    {
        $this->authorize('*accountant');

        $applications = Application::with('student')->latest()->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        $selected_application_id = $request->get('application_id');
        $accounts = OfficeAccount::where('status', 'active')->get();

        return view('admin.payments.create', compact('applications', 'users', 'selected_application_id', 'accounts'));
    }

    public function store(Request $request)
    {
        $this->authorize('*accountant');

        $validated = $this->validatePayment($request);

        $application = Application::findOrFail($validated['application_id']);
        $validated['student_id'] = $application->student_id;

        try {
            DB::beginTransaction();

            $payment = Payment::create($validated);

            // NEW: Double-Entry Ledger Posting
            if ($validated['payment_status'] === 'completed') {
                $officeAccount = OfficeAccount::find($payment->office_account_id);
                $period = AccountingPeriod::where('is_closed', false)
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->first();

                if ($officeAccount && $officeAccount->chart_of_account_id && $period) {
                    $entry = JournalEntry::create([
                        'period_id' => $period->id,
                        'date' => $payment->payment_date ?? now(),
                        'reference_number' => 'JV-PAY-' . $payment->receipt_number,
                        'note' => 'Automated post for Student Payment: ' . $payment->receipt_number,
                        'status' => 'posted',
                        'created_by' => Auth::id(),
                    ]);

                    $payment->update(['journal_entry_id' => $entry->id]);

                    // Debit Bank/Cash
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $officeAccount->chart_of_account_id,
                        'debit' => $payment->amount,
                        'credit' => 0,
                        'description' => 'Payment received in ' . $officeAccount->account_name,
                    ]);

                    // Credit Student Fees Income (Finding dynamic head)
                    $incomeHead = ChartOfAccount::where('name', 'like', '%Student Fee%')
                        ->orWhere('code', '4001') // Common income code
                        ->first() ?? ChartOfAccount::where('type', 'revenue')->first();

                    if ($incomeHead) {
                        JournalEntryItem::create([
                            'journal_entry_id' => $entry->id,
                            'chart_of_account_id' => $incomeHead->id,
                            'debit' => 0,
                            'credit' => $payment->amount,
                            'description' => 'Student Payment Credit: ' . $application->student->first_name,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()
                ->route('admin.payments.index')
                ->with('success', 'Payment recorded and Ledger posted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Posting failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Payment $payment)
    {
        $this->authorize('*accountant');

        $applications = Application::with('student')->latest()->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        $accounts = OfficeAccount::where('status', 'active')->get();

        return view('admin.payments.edit', compact('payment', 'applications', 'users', 'accounts'));
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorize('*accountant');

        $validated = $this->validatePayment($request);

        $application = Application::findOrFail($validated['application_id']);
        $validated['student_id'] = $application->student_id;

        $payment->update($validated);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $this->authorize('*accountant');

        try {
            DB::beginTransaction();
            if ($payment->journal_entry_id) {
                JournalEntry::find($payment->journal_entry_id)?->delete();
            }
            $payment->delete();
            DB::commit();

            return redirect()
                ->route('admin.payments.index')
                ->with('success', 'Payment and ledger entry deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Deletion failed: ' . $e->getMessage()]);
        }
    }

    // public function downloadInvoice(Payment $payment)
    // {
    //     $this->authorize('*accountant');

    //     $payment->load(['student', 'collector', 'application.university.country', 'application.course', 'application.intake']);
    //     $settings = Setting::pluck('value', 'key')->all();

    //     $pdf = Pdf::loadView('admin.payments.invoice', compact('payment', 'settings'));

    //     $filename = 'Invoice_' . ($payment->receipt_number ?: $payment->id) . '.pdf';

    //     return $pdf->download($filename);
    // }

      public function downloadInvoice(Payment $payment)
    {
        $this->authorize('*accountant');

        $payment->load(['student', 'collector', 'account', 'application.university.country', 'application.course', 'application.intake', 'invoice.payments', 'invoice.items']);
        $settings = Setting::pluck('value', 'key')->all();

        // Calculate remaining balance considering all payments
        $totalPaid = $payment->invoice ? $payment->invoice->payments->sum('amount') : $payment->amount;
        $remainingBalance = $payment->invoice ? max(0, $payment->invoice->total_amount - $totalPaid) : 0;

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);
        $mpdf->WriteHTML(view('admin.payments.invoice', compact('payment', 'settings', 'remainingBalance', 'totalPaid'))->render());

        $filename = 'Invoice_' . ($payment->receipt_number ?: $payment->id) . '.pdf';

        return $mpdf->Output($filename, 'D');
    }

    private function validatePayment(Request $request): array
    {
        return $request->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_type' => ['required', Rule::in(['advance', 'partial', 'final'])],
            'payment_date' => ['nullable', 'date'],
            'receipt_number' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['required', Rule::in(['pending', 'completed'])],
            'office_account_id' => ['nullable', 'exists:office_accounts,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    public function getApplicationBalance(Request $request)
    {
        $request->validate(['application_id' => 'required|exists:applications,id']);

        $application = Application::with('payments')->findOrFail($request->application_id);

        $totalPaid = $application->payments()
            ->whereIn('payment_status', ['pending', 'completed'])
            ->sum('amount');

        return response()->json([
            'total_fee' => $application->total_fee,
            'total_paid' => $totalPaid,
            'balance' => $application->total_fee - $totalPaid
        ]);
    }

    public function getApplicationInvoices(Request $request)
    {
        $request->validate(['application_id' => 'required|exists:applications,id']);

        $invoices = Invoice::where('application_id', $request->application_id)
            ->with(['payments' => function ($q) {
                $q->whereIn('payment_status', ['pending', 'completed']);
            }])
            ->get()
            ->map(function ($invoice) {
                $totalPaid = $invoice->payments->sum('amount');
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'total_amount' => $invoice->total_amount,
                    'total_paid' => $totalPaid,
                    'remaining' => $invoice->total_amount - $totalPaid,
                    'status' => $invoice->status,
                ];
            });

        return response()->json([
            'invoices' => $invoices
        ]);
    }

    public function report(Request $request)
    {
        $this->authorize('*accountant');

        $query = Payment::with(['student', 'application', 'collector']);

        // Date range filter
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('payment_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('payment_date', '<=', $endDate);
        }

        // Status filter
        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        // Type filter
        if ($type = $request->get('payment_type')) {
            $query->where('payment_type', $type);
        }

        $payments = $query->latest()->get();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);

        $html = view('admin.payments.pdf', compact('payments', 'request'))->render();
        $mpdf->WriteHTML($html);

        $outputMode = $request->get('output') === 'download' ? 'D' : 'I';

        return response($mpdf->Output('payments-report.pdf', $outputMode), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
