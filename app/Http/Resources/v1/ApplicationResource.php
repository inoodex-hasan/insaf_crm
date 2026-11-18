<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_id' => $this->application_id,
            'status' => $this->status,
            'tuition_fee' => (float) $this->tuition_fee,
            'total_fee' => (float) $this->total_fee,
            'notes' => $this->notes,
            'internal_notes' => $this->internal_notes,
            'application_priority' => $this->application_priority,

            // Milestone Flags & Dates
            'offer_letter_received' => (bool) $this->offer_letter_received,
            'offer_letter_received_date' => $this->offer_letter_received_date?->format('Y-m-d'),
            'vfs_appointment' => (bool) $this->vfs_appointment,
            'vfs_appointment_date' => $this->vfs_appointment_date?->format('Y-m-d'),
            'file_submission' => (bool) $this->file_submission,
            'file_submission_date' => $this->file_submission_date?->format('Y-m-d'),
            'visa_status' => $this->visa_status,
            'visa_decision_date' => $this->visa_decision_date?->format('Y-m-d'),
            'visa_approval_date' => $this->visa_approval_date?->format('Y-m-d'),

            // Financial & Fee Statuses
            'tuition_fee_status' => $this->tuition_fee_status,
            'service_charge_status' => $this->service_charge_status,
            'security_deposit_status' => (bool) $this->security_deposit_status,
            'cvu_fee_status' => (bool) $this->cvu_fee_status,
            'admission_fee_status' => (bool) $this->admission_fee_status,
            'final_payment_status' => (bool) $this->final_payment_status,
            'emgs_payment_status' => (bool) $this->emgs_payment_status,
            'emgs_score' => $this->emgs_score,
            'final_status' => $this->final_status,
            'documents_checklist' => $this->documents_checklist,

            // Linked Relations
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'full_name' => $this->student->full_name,
                    'phone' => $this->student->phone,
                    'email' => $this->student->email,
                    'passport_number' => $this->student->passport_number,
                ];
            }),
            'university' => $this->whenLoaded('university', function () {
                return [
                    'id' => $this->university->id,
                    'name' => $this->university->name,
                    'country' => $this->university->country?->name,
                ];
            }),
            'course' => $this->whenLoaded('course', function () {
                return [
                    'id' => $this->course->id,
                    'name' => $this->course->name,
                    'tuition_fee' => (float) $this->course->tuition_fee,
                ];
            }),
            'intake' => $this->whenLoaded('intake', function () {
                return [
                    'id' => $this->intake->id,
                    'name' => $this->intake->intake_name,
                ];
            }),
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
