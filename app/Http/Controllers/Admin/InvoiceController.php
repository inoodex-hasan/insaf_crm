<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\University;
use App\Models\ChartOfAccount;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\CourseIntake;
use Mpdf\Mpdf;

class InvoiceController extends Controller
{

    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request)
    {
        $this->authorize('*consultant|*application|*accountant');

        $query = Invoice::with(['student', 'application', 'university']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('id_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('university', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalCount = Invoice::count();
        $paidCount = Invoice::where('status', 'paid')->count();

        return view('admin.accounts.invoices.index', compact('invoices', 'totalCount', 'paidCount'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request)
    {
        $this->authorize('*consultant|*application|*accountant');
        $students = Student::orderBy('first_name')->get();
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        $applications = Application::with(['student', 'university', 'course', 'intake'])->orderBy('id', 'desc')->get();

        $selectedApplication = null;
        if ($request->has('application_id')) {
            $selectedApplication = $applications->firstWhere('id', $request->application_id);
        }

        return view('admin.accounts.invoices.create', compact('students', 'accounts', 'applications', 'selectedApplication'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('*consultant|*application|*accountant');
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'invoice_number' => 'nullable|string|max:50|unique:invoices,invoice_number',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $application = \App\Models\Application::find($request->application_id);

            $totalAmount = collect($request->items)->sum(fn($i) => (float) $i['quantity'] * (float) $i['unit_price']);

            $invoice = Invoice::create([
                'student_id' => $application->student_id,
                'application_id' => $request->application_id,
                'university_id' => $application->university_id,
                'invoice_number' => $request->invoice_number ?? $this->generateInvoiceNumber(),
                'date' => $request->date,
                'due_date' => $request->due_date,
                'total_amount' => $totalAmount,
                'status' => 'draft',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $subtotal = (float) $item['quantity'] * (float) $item['unit_price'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'chart_of_account_id' => $item['chart_of_account_id'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                    'tax_amount' => 0.00,
                    'total' => $subtotal,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.invoices.index')->with('success', 'Student invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Invoice creation failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['items.chartOfAccount', 'student', 'application', 'university']);
        return view('admin.accounts.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['items', 'application.student', 'application.university', 'application.course', 'application.intake']);
        $students = Student::orderBy('first_name')->get();
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        $applications = Application::with(['student', 'university', 'course', 'intake'])->orderBy('id', 'desc')->get();

        return view('admin.accounts.invoices.edit', compact('invoice', 'students', 'accounts', 'applications'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'invoice_number' => 'nullable|string|max:50|unique:invoices,invoice_number,' . $invoice->id,
            'status' => 'required|in:draft,sent,paid,partially_paid,void',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $application = Application::find($request->application_id);
            $totalAmount = collect($request->items)->sum(fn($i) => (float) $i['quantity'] * (float) $i['unit_price']);

            $invoice->update([
                'student_id' => $application->student_id,
                'application_id' => $request->application_id,
                'university_id' => $application->university_id,
                'invoice_number' => $request->invoice_number ?: $invoice->invoice_number,
                'date' => $request->date,
                'due_date' => $request->due_date,
                'total_amount' => $totalAmount,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Delete old items and recreate
            $invoice->items()->delete();

            foreach ($request->items as $item) {
                $subtotal = (float) $item['quantity'] * (float) $item['unit_price'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'chart_of_account_id' => $item['chart_of_account_id'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                    'tax_amount' => 0.00,
                    'total' => $subtotal,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.invoices.index')->with('success', 'Invoice updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Invoice update failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // Prevent deletion if payments are linked (financial records)
        if ($invoice->payments()->exists()) {
            $count = $invoice->payments()->count();
            return redirect()->back()
                ->with('warning', "Cannot delete this Invoice because it has {$count} linked Payment(s). Please remove all payments first.");
        }

        // Cascade delete invoice items first, then the invoice
        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->back()->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['student', 'university', 'university.country', 'course', 'intake', 'items']);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
        ]);

        $html = view('admin.accounts.invoices.pdf', compact('invoice'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Invoice_' . $invoice->invoice_number . '.pdf"');
    }

    /**
     * Generate sequential invoice number starting from 1001
     */
    private function generateInvoiceNumber(): string
    {
        $datePart = now()->format('Ymd');

        // Find the last invoice number for today
        $lastInvoice = Invoice::where('invoice_number', 'like', "INV-{$datePart}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice && preg_match('/INV-\d{8}-(\d+)$/', $lastInvoice->invoice_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1000;
        }

        return "INV-{$datePart}-{$nextNumber}";
    }
}
