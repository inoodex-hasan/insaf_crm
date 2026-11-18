<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Build document URLs safely
        $formattedDocuments = [];
        if (!empty($this->documents) && is_array($this->documents)) {
            foreach ($this->documents as $doc) {
                $formattedDocuments[] = [
                    'name' => $doc['name'] ?? 'Document',
                    'path' => $doc['path'] ?? '',
                    'url' => !empty($doc['path']) ? Storage::disk('public')->url($doc['path']) : null,
                ];
            }
        }

        $formattedTranslationDocs = [];
        if (!empty($this->translation_documents) && is_array($this->translation_documents)) {
            foreach ($this->translation_documents as $tdoc) {
                $formattedTranslationDocs[] = [
                    'name' => $tdoc['name'] ?? 'Translation Document',
                    'path' => $tdoc['path'] ?? '',
                    'url' => !empty($tdoc['path']) ? Storage::disk('public')->url($tdoc['path']) : null,
                ];
            }
        }

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
            'address' => $this->address,
            'ssc_result' => $this->ssc_result,
            'hsc_result' => $this->hsc_result,
            'ielts_score' => $this->ielts_score,
            'subject' => $this->subject,
            'package_amount' => (float) $this->package_amount,
            'total_paid' => (float) $this->total_paid_amount,
            'due_balance' => (float) $this->due_balance,
            'remarks' => $this->remarks,
            'available_remarks' => $this->available_remarks,
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

            'documents' => $formattedDocuments,
            'translation_documents' => $formattedTranslationDocs,

            'applications' => $this->whenLoaded('applications', function () {
                return $this->applications->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'application_id' => $app->application_id,
                        'university' => $app->university?->name,
                        'course' => $app->course?->name,
                        'status' => $app->status,
                        'visa_status' => $app->visa_status,
                        'created_at' => $app->created_at?->format('Y-m-d'),
                    ];
                });
            }),

            'payments' => $this->whenLoaded('payments', function () {
                return $this->payments->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'amount' => (float) $p->amount,
                        'payment_date' => $p->payment_date?->format('Y-m-d'),
                        'payment_method' => $p->payment_method,
                        'reference_number' => $p->reference_number,
                    ];
                });
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
