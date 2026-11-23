<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentPaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentPaymentStatusController extends Controller
{
    /**
     * Display a listing of student payment statuses with counts and search.
     */
    public function index(Request $request)
    {
        $query = StudentPaymentStatus::query();

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $statuses = $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->paginate(20)->withQueryString();
        $totalCount = StudentPaymentStatus::count();
        $activeCount = StudentPaymentStatus::where('is_active', true)->count();

        return view('admin.student-payment-statuses.index', compact('statuses', 'totalCount', 'activeCount'));
    }

    /**
     * Store a newly created payment status (supports AJAX for quick-add modal).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50', 'unique:student_payment_statuses,code'],
            'color' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $code = !empty($validated['code'])
            ? \Illuminate\Support\Str::slug($validated['code'], '_')
            : StudentPaymentStatus::generateCode($validated['name']);

        $maxSort = StudentPaymentStatus::max('sort_order') ?? 0;

        $status = StudentPaymentStatus::create([
            'name' => $validated['name'],
            'code' => $code,
            'color' => $validated['color'] ?? 'info',
            'sort_order' => $validated['sort_order'] ?? ($maxSort + 1),
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment status created successfully.',
                'status' => $status,
            ]);
        }

        return redirect()->route('admin.student-payment-statuses.index')->with('success', 'Payment status created successfully.');
    }

    /**
     * Update the specified payment status.
     */
    public function update(Request $request, StudentPaymentStatus $studentPaymentStatus)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', Rule::unique('student_payment_statuses', 'code')->ignore($studentPaymentStatus->id)],
            'color' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $studentPaymentStatus->update([
            'name' => $validated['name'],
            'code' => \Illuminate\Support\Str::slug($validated['code'], '_'),
            'color' => $validated['color'] ?? 'info',
            'sort_order' => $validated['sort_order'] ?? $studentPaymentStatus->sort_order,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $studentPaymentStatus->is_active,
        ]);

        return redirect()->route('admin.student-payment-statuses.index')->with('success', 'Payment status updated successfully.');
    }

    /**
     * Remove the specified payment status.
     */
    public function destroy(StudentPaymentStatus $studentPaymentStatus)
    {
        $studentPaymentStatus->delete();

        return redirect()->route('admin.student-payment-statuses.index')->with('success', 'Payment status deleted successfully.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(StudentPaymentStatus $studentPaymentStatus)
    {
        $studentPaymentStatus->is_active = !$studentPaymentStatus->is_active;
        $studentPaymentStatus->save();

        return redirect()->route('admin.student-payment-statuses.index')->with(
            'success',
            "Status '{$studentPaymentStatus->name}' is now " . ($studentPaymentStatus->is_active ? 'Active' : 'Inactive') . '.'
        );
    }
}
