<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\ExpenseResource;
use App\Models\Expense;
use App\Models\Salary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ExpenseApiController extends BaseApiController
{
    /**
     * Display a listing of expenses with summary metrics.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('*accountant');

        $query = Expense::with(['creator', 'chartOfAccount', 'account', 'staff', 'country', 'university']);

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

        // Category filter (chart of account name or ID)
        if ($category = $request->get('category')) {
            $query->whereHas('chartOfAccount', function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }
        if ($chartOfAccountId = $request->get('chart_of_account_id')) {
            $query->where('chart_of_account_id', $chartOfAccountId);
        }

        // Date range filter
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('expense_date', '<=', $endDate);
        }

        // Calculate summary metrics on current filtered set
        $totalExpenses = (float) (clone $query)->sum('amount');
        $totalInvestments = (float) (clone $query)->where('is_investment', true)->sum('amount');
        $totalGeneral = (float) (clone $query)->where('is_investment', false)->sum('amount');

        $perPage = (int) $request->get('per_page', 15);
        $expenses = $query->latest('expense_date')->latest('id')->paginate($perPage);

        return $this->sendPaginated(
            ExpenseResource::collection($expenses),
            'Expenses retrieved successfully.',
            200,
            [
                'metrics' => [
                    'total_expenses' => $totalExpenses,
                    'total_investments' => $totalInvestments,
                    'total_general' => $totalGeneral,
                ],
            ]
        );
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request): JsonResponse
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

        $expense->load(['creator', 'chartOfAccount', 'account', 'staff', 'country', 'university']);

        return $this->sendResponse(
            new ExpenseResource($expense),
            'Expense recorded successfully.',
            201
        );
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense): JsonResponse
    {
        $this->authorize('*accountant');

        $expense->load(['creator', 'chartOfAccount', 'account', 'staff', 'country', 'university']);

        return $this->sendResponse(
            new ExpenseResource($expense),
            'Expense details retrieved successfully.'
        );
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense): JsonResponse
    {
        $this->authorize('*accountant');

        $validated = $this->validateExpense($request);

        $expense->update($validated);

        // Re-check salary payment status if linked
        if ($expense->salary_id) {
            $salary = Salary::find($expense->salary_id);
            if ($salary) {
                $totalPaid = Expense::where('salary_id', $expense->salary_id)->sum('amount');
                $salary->paid_amount = $totalPaid;

                if ($totalPaid >= $salary->net_salary) {
                    $salary->payment_status = 'paid';
                } elseif ($totalPaid > 0) {
                    $salary->payment_status = 'partial';
                } else {
                    $salary->payment_status = 'unpaid';
                }
                $salary->save();
            }
        }

        $expense->load(['creator', 'chartOfAccount', 'account', 'staff', 'country', 'university']);

        return $this->sendResponse(
            new ExpenseResource($expense),
            'Expense updated successfully.'
        );
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense): JsonResponse
    {
        $this->authorize('*accountant');

        try {
            $salaryId = $expense->salary_id;
            $expense->delete();

            // Re-evaluate salary payment status if linked
            if ($salaryId) {
                $salary = Salary::find($salaryId);
                if ($salary) {
                    $totalPaid = Expense::where('salary_id', $salaryId)->sum('amount');
                    $salary->paid_amount = $totalPaid;
                    if ($totalPaid >= $salary->net_salary) {
                        $salary->payment_status = 'paid';
                    } elseif ($totalPaid > 0) {
                        $salary->payment_status = 'partial';
                    } else {
                        $salary->payment_status = 'unpaid';
                    }
                    $salary->save();
                }
            }

            return $this->sendResponse(null, 'Expense deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint')) {
                return $this->sendError('Cannot delete this expense because it has related records.', [], 422);
            }
            throw $e;
        }
    }

    /**
     * Validate expense payload.
     */
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
}
