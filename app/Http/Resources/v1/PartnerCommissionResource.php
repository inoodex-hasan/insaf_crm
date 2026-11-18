<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerCommissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'commission_type' => $this->commission_type,
            'direction' => $this->direction,
            'agency_referral_type' => $this->agency_referral_type,
            
            // Linked Entities
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'name' => trim($this->student->first_name . ' ' . $this->student->last_name),
                    'passport' => $this->student->passport_number,
                    'phone' => $this->student->phone,
                    'country' => $this->student->country?->name,
                ];
            }),
            'partner_agency' => $this->whenLoaded('partnerAgency', function () {
                return [
                    'id' => $this->partnerAgency->id,
                    'name' => $this->partnerAgency->name,
                    'contact_person' => $this->partnerAgency->contact_person,
                    'phone' => $this->partnerAgency->phone,
                    'email' => $this->partnerAgency->email,
                ];
            }),
            'university' => $this->whenLoaded('university', function () {
                return [
                    'id' => $this->university->id,
                    'name' => $this->university->name,
                    'country' => $this->university->country?->name,
                ];
            }),
            'country' => $this->whenLoaded('country', function () {
                return [
                    'id' => $this->country->id,
                    'name' => $this->country->name,
                ];
            }),
            'handover_officer' => $this->whenLoaded('handoverOfficer', function () {
                return [
                    'id' => $this->handoverOfficer->id,
                    'name' => $this->handoverOfficer->name,
                ];
            }),

            // Economics & Rates
            'rate_type' => $this->rate_type,
            'rate_value' => (float) $this->rate_value,
            'base_amount' => $this->base_amount ? (float) $this->base_amount : null,
            'currency' => $this->currency,
            'exchange_rate' => (float) $this->exchange_rate,
            'total_commission' => (float) $this->total_commission,
            'total_amount_bdt' => (float) $this->total_amount_bdt,
            'paid_amount_bdt' => (float) $this->paid_amount_bdt,
            'due_amount_bdt' => (float) $this->due_amount_bdt,

            // Statuses & Stages
            'workflow_status' => $this->workflow_status,
            'payment_status' => $this->payment_status,
            'handover_date' => $this->handover_date?->format('Y-m-d'),
            'processing_stage' => $this->processing_stage,
            'agreement_reference' => $this->agreement_reference,
            'processing_notes' => $this->processing_notes,

            // Settlements History
            'settlements' => PartnerCommissionSettlementResource::collection($this->whenLoaded('settlements')),
            
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
