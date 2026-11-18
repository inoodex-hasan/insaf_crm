<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'passport_number' => $this->passport_number,
            'passport_validity' => $this->passport_validity?->format('Y-m-d'),
            'dob' => $this->dob?->format('Y-m-d'),
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'sponsor_phone' => $this->sponsor_phone,
            'subject' => $this->subject,
            'package_amount' => (float) $this->package_amount,
            'total_paid' => (float) $this->total_paid_amount,
            'due_balance' => (float) $this->due_balance,
            'remarks' => $this->remarks,
            'notes' => $this->notes,

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
            'course' => $this->whenLoaded('course', function () {
                return [
                    'id' => $this->course->id,
                    'name' => $this->course->name,
                ];
            }),
            'intake' => $this->whenLoaded('intake', function () {
                return [
                    'id' => $this->intake->id,
                    'name' => $this->intake->intake_name,
                ];
            }),

            'assigned_marketing' => $this->whenLoaded('marketingAssignee', function () {
                return [
                    'id' => $this->marketingAssignee->id,
                    'name' => $this->marketingAssignee->name,
                ];
            }),
            'assigned_consultant' => $this->whenLoaded('consultantAssignee', function () {
                return [
                    'id' => $this->consultantAssignee->id,
                    'name' => $this->consultantAssignee->name,
                ];
            }),
            'assigned_application' => $this->whenLoaded('applicationAssignee', function () {
                return [
                    'id' => $this->applicationAssignee->id,
                    'name' => $this->applicationAssignee->name,
                ];
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
