<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\{Expense, ChartOfAccount, OfficeAccount, Salary, User, Country, University};
use Mpdf\Mpdf;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('*accountant');

        $query = Expense::with(['creator', 'chartOfAccount', 'staff', 'country', 'university']);

        // Search filter
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('investment_purpose', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('country', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('university', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Investment filter (all, general, investment)
        if ($investmentFilter = $request->get('investment_filter')) {
            if ($investmentFilter === 'investment') {
                $query->where('is_investment', true);
            } elseif ($investmentFilter === 'general') {
                $query->where('is_investment', false);
            }
        }

        // Staff filter
        if ($staffId = $request->get('staff_id')) {
            $query->where('staff_id', $staffId);
        }

        // Country filter
        if ($countryId = $request->get('country_id')) {
            $query->where('country_id', $countryId);
        }

        // Category filter (using chart_of_account)
        if ($category = $request->get('category')) {
            $query->whereHas('chartOfAccount', function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }

        // Date range filter
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('expense_date', '<=', $endDate);
        }

        // Calculate summary metrics on current filtered set
        $totalExpenses = (clone $query)->sum('amount');
        $totalInvestments = (clone $query)->where('is_investment', true)->sum('amount');
        $totalGeneral = (clone $query)->where('is_investment', false)->sum('amount');

        $expenses = $query->latest('expense_date')->latest('id')->paginate(15)->withQueryString();

        // Fetch taxonomies and helper lists
        $categories = ChartOfAccount::where('is_active', true)
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();
        $accounts = OfficeAccount::where('status', 'active')->get();
        $staffList = User::orderBy('name')->get(['id', 'name']);
        $countries = Country::whereIn('status', [1, '1', 'active'])->orderBy('name')->get(['id', 'name']);
        $universities = University::whereIn('status', [1, '1', 'active'])->orderBy('name')->get(['id', 'name', 'country_id']);

        return view('admin.expenses.index', compact(
            'expenses',
            'categories',
            'accounts',
            'staffList',
            'countries',
            'universities',
            'totalExpenses',
            'totalInvestments',
            'totalGeneral'
        ));
    }

    public function create(Request $request)
    {
        $this->authorize('*accountant');

        $categories = ChartOfAccount::where('is_active', true)
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();
        $accounts = OfficeAccount::where('status', 'active')->get();
        $staffList = User::orderBy('name')->get(['id', 'name']);
        $countries = Country::whereIn('status', [1, '1', 'active'])->orderBy('name')->get(['id', 'name']);
        $universities = University::whereIn('status', [1, '1', 'active'])->orderBy('name')->get(['id', 'name', 'country_id']);
        $isInvestment = $request->boolean('is_investment');

        return view('admin.expenses.create', compact(
            'categories',
            'accounts',
            'staffList',
            'countries',
            'universities',
            'isInvestment'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('*accountant');

        $validated = $this->validateExpense($request);

        $expense = Expense::create($validated);

        // Update salary payment status if linked
        if ($expense->salary_id) {
            $salary = Salary::find($expense->salary_id);
            if ($salary) {
                $totalPaid = Expense::where('salary_id', $expense->salary_id)->sum('amount');
                $salary->paid_amount = $totalPaid;

                if ($totalPaid >= $salary->net_salary) {
                    $salary->payment_status = 'paid';
                } elseif ($totalPaid > 0) {
                    $salary->payment_status = 'partial';
                }
                $salary->save();
            }
        }

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $this->authorize('*accountant');

        $categories = ChartOfAccount::where('is_active', true)
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();
        $accounts = OfficeAccount::where('status', 'active')->get();
        $staffList = User::orderBy('name')->get(['id', 'name']);
        $countries = Country::whereIn('status', [1, '1', 'active'])->orderBy('name')->get(['id', 'name']);
        $universities = University::whereIn('status', [1, '1', 'active'])->orderBy('name')->get(['id', 'name', 'country_id']);

        return view('admin.expenses.edit', compact(
            'expense',
            'categories',
            'accounts',
            'staffList',
            'countries',
            'universities'
        ));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('*accountant');

        $validated = $this->validateExpense($request);

        $expense->update($validated);

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('*accountant');

        return $this->safeDelete($expense, 'admin.expenses.index', [], 'Expense deleted successfully.');
    }

    public function downloadPdf(Expense $expense)
    {
        $this->authorize('*accountant');

        $expense->load(['creator', 'chartOfAccount', 'account', 'staff', 'country', 'university']);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);

        $html = view('admin.expenses.pdf', compact('expense'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="expense-' . $expense->id . '.pdf"');
    }

    private function validateExpense(Request $request): array
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'chart_of_account_id' => ['required', 'exists:chart_of_accounts,id'],
            'payment_method' => ['required', 'in:cash,credit,bank_transfer,mobile_banking,cheque'],
            'office_account_id' => ['nullable', 'exists:office_accounts,id'],
            'salary_id' => ['nullable', 'exists:salaries,id'],
            'notes' => ['nullable', 'string'],
            'is_investment' => ['nullable'],
            'staff_id' => ['nullable', 'required_if:is_investment,1,true', 'exists:users,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'university_id' => ['nullable', 'exists:universities,id'],
            'investment_purpose' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['is_investment'] = $request->boolean('is_investment');
        if (!$validated['is_investment']) {
            $validated['staff_id'] = null;
            $validated['country_id'] = null;
            $validated['university_id'] = null;
            $validated['investment_purpose'] = null;
        }

        return $validated;
    }

    public function preview(Request $request)
    {
        $this->authorize('*accountant');

        $query = Expense::with(['creator', 'chartOfAccount', 'staff', 'country', 'university']);

        // Apply same filters as index
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('investment_purpose', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($investmentFilter = $request->get('investment_filter')) {
            if ($investmentFilter === 'investment') {
                $query->where('is_investment', true);
            } elseif ($investmentFilter === 'general') {
                $query->where('is_investment', false);
            }
        }

        if ($staffId = $request->get('staff_id')) {
            $query->where('staff_id', $staffId);
        }

        if ($countryId = $request->get('country_id')) {
            $query->where('country_id', $countryId);
        }

        if ($category = $request->get('category')) {
            $query->whereHas('chartOfAccount', function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('expense_date', '<=', $endDate);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $totalAmount = $expenses->sum('amount');

        // Generate PDF using mPDF for preview
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);

        $html = view('admin.expenses.report', compact('expenses', 'totalAmount', 'request'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="expenses-report-preview.pdf"');
    }

    public function download(Request $request)
    {
        $this->authorize('*accountant');

        $query = Expense::with(['creator', 'chartOfAccount', 'staff', 'country', 'university']);

        // Apply same filters as index
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('investment_purpose', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($investmentFilter = $request->get('investment_filter')) {
            if ($investmentFilter === 'investment') {
                $query->where('is_investment', true);
            } elseif ($investmentFilter === 'general') {
                $query->where('is_investment', false);
            }
        }

        if ($staffId = $request->get('staff_id')) {
            $query->where('staff_id', $staffId);
        }

        if ($countryId = $request->get('country_id')) {
            $query->where('country_id', $countryId);
        }

        if ($category = $request->get('category')) {
            $query->whereHas('chartOfAccount', function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('expense_date', '<=', $endDate);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $totalAmount = $expenses->sum('amount');

        // Generate PDF using mPDF for download
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);

        $html = view('admin.expenses.report', compact('expenses', 'totalAmount', 'request'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="expenses-report-' . now()->format('Y-m-d') . '.pdf"');
    }
}
