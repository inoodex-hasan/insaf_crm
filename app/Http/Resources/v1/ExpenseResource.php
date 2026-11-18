<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'expense_date' => $this->expense_date?->format('Y-m-d'),
            'description' => $this->description,
            'vendor' => $this->vendor,
            'payment_method' => $this->payment_method,
            'is_investment' => (bool) $this->is_investment,
            'investment_purpose' => $this->investment_purpose,

            // Linked details
            'category' => $this->whenLoaded('chartOfAccount', function () {
                return [
                    'id' => $this->chartOfAccount->id,
                    'code' => $this->chartOfAccount->code,
                    'name' => $this->chartOfAccount->name,
                ];
            }),
            'office_account' => $this->whenLoaded('account', function () {
                return [
                    'id' => $this->account->id,
                    'name' => $this->account->account_name,
                    'type' => $this->account->account_type,
                ];
            }),
            'staff' => $this->whenLoaded('staff', function () {
                return [
                    'id' => $this->staff->id,
                    'name' => $this->staff->name,
                    'designation' => $this->staff->designation,
                ];
            }),
            'country' => $this->whenLoaded('country', function () {
                return [
                    'id' => $this->country->id,
                    'name' => $this->country->name,
                ];
            }),
            'university' => $this->whenLoaded('university', function () {
                return [
                    'id' => $this->university->id,
                    'name' => $this->university->name,
                ];
            }),

            'receipt_url' => $this->receipt_attachment ? Storage::url($this->receipt_attachment) : null,
            'created_by' => $this->creator?->name,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
