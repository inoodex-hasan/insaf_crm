<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_name' => $this->account_name,
            'account_type' => $this->account_type,
            'provider_name' => $this->provider_name,
            'account_number' => $this->account_number,
            'branch_name' => $this->branch_name,
            'opening_balance' => (float) $this->opening_balance,
            'remaining_balance' => (float) $this->remaining_balance,
            'status' => $this->status,
            'notes' => $this->notes,
            'chart_of_account_id' => $this->chart_of_account_id,
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
