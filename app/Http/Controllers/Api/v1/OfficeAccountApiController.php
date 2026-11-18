<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\OfficeAccountResource;
use App\Models\OfficeAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class OfficeAccountApiController extends BaseApiController
{
    /**
     * Display a listing of office accounts with computed live balances.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('*accountant');

        $query = OfficeAccount::with(['creator'])
            ->withSum('incomingTransactions as total_income', 'credit')
            ->withSum('outgoingTransactions as total_expense', 'debit');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                    ->orWhere('provider_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('account_type')) {
            $query->where('account_type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $perPage = (int) $request->get('per_page', 15);
        $accounts = $query->latest()->paginate($perPage);

        return $this->sendPaginated(
            OfficeAccountResource::collection($accounts),
            'Office accounts retrieved successfully.'
        );
    }

    /**
     * Store a newly created office account.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('*accountant');

        $validated = $this->validateAccount($request);

        $account = OfficeAccount::create($validated);
        $account->load('creator');

        return $this->sendResponse(
            new OfficeAccountResource($account),
            'Office account created successfully.',
            201
        );
    }

    /**
     * Display the specified office account.
     */
    public function show(OfficeAccount $officeAccount): JsonResponse
    {
        $this->authorize('*accountant');

        $officeAccount->load('creator');

        return $this->sendResponse(
            new OfficeAccountResource($officeAccount),
            'Office account details retrieved successfully.'
        );
    }

    /**
     * Update the specified office account.
     */
    public function update(Request $request, OfficeAccount $officeAccount): JsonResponse
    {
        $this->authorize('*accountant');

        $validated = $this->validateAccount($request);

        $officeAccount->update($validated);
        $officeAccount->load('creator');

        return $this->sendResponse(
            new OfficeAccountResource($officeAccount),
            'Office account updated successfully.'
        );
    }

    /**
     * Remove the specified office account.
     */
    public function destroy(OfficeAccount $officeAccount): JsonResponse
    {
        $this->authorize('*accountant');

        try {
            $officeAccount->delete();
            return $this->sendResponse(null, 'Office account deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint')) {
                return $this->sendError('Cannot delete this office account because it has related ledger or payment records.', [], 422);
            }
            throw $e;
        }
    }

    /**
     * Validate office account payload.
     */
    private function validateAccount(Request $request): array
    {
        return $request->validate([
            'account_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', Rule::in(['bank', 'mfs', 'cash'])],
            'provider_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:100'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
