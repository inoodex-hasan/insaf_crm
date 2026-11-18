<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->format('Y-m-d'),
            'check_in' => $this->check_in?->toIso8601String(),
            'check_in_time' => $this->check_in?->format('h:i A'),
            'check_out' => $this->check_out?->toIso8601String(),
            'check_out_time' => $this->check_out?->format('h:i A'),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'is_late' => (bool) $this->is_late,
            'late_minutes' => $this->late_minutes,
            'total_hours' => (float) $this->total_hours,
            'office_name' => $this->office_name,
            'check_in_distance_meters' => $this->check_in_distance_meters,
            'check_out_distance_meters' => $this->check_out_distance_meters,
            'is_manually_adjusted' => (bool) $this->is_manually_adjusted,
            'adjustment_reason' => $this->adjustment_reason,
            'notes' => $this->notes,

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'adjuster' => $this->whenLoaded('adjuster', function () {
                return [
                    'id' => $this->adjuster->id,
                    'name' => $this->adjuster->name,
                ];
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
