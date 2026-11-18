<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerCommissionSettlementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'partner_commission_id' => $this->partner_commission_id,
            'office_account_id' => $this->office_account_id,
            'office_account_name' => $this->officeAccount?->account_name,
            'journal_entry_id' => $this->journal_entry_id,
            'journal_reference' => $this->journalEntry?->reference_number,
            'settlement_type' => $this->settlement_type,
            'amount_bdt' => (float) $this->amount_bdt,
            'payment_method' => $this->payment_method,
            'reference_no' => $this->reference_no,
            'settlement_date' => $this->settlement_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'created_by' => $this->creator?->name,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
