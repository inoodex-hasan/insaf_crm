<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_name' => $this->student_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'current_education' => $this->current_education,
            'source' => $this->source,
            'status' => $this->status,
            'priority' => $this->priority,
            'notes' => $this->notes,
            'last_contacted_at' => $this->last_contacted_at?->toIso8601String(),
            'next_follow_up_at' => $this->next_follow_up_at?->format('Y-m-d'),
            'follow_up_history' => $this->follow_up_history,

            'preferred_country' => $this->whenLoaded('country', function () {
                return [
                    'id' => $this->country->id,
                    'name' => $this->country->name,
                ];
            }),
            'preferred_course' => $this->whenLoaded('course', function () {
                return [
                    'id' => $this->course->id,
                    'name' => $this->course->name,
                ];
            }),
            'collector' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            'consultant' => $this->whenLoaded('consultant', function () {
                return [
                    'id' => $this->consultant->id,
                    'name' => $this->consultant->name,
                ];
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
