<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_in_ip',
        'check_out_ip',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'check_in_distance_meters',
        'check_out_distance_meters',
        'status',
        'is_late',
        'late_minutes',
        'total_hours',
        'device_info',
        'is_manually_adjusted',
        'adjusted_by',
        'adjustment_reason',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'check_in_lat' => 'decimal:8',
        'check_in_lng' => 'decimal:8',
        'check_out_lat' => 'decimal:8',
        'check_out_lng' => 'decimal:8',
        'check_in_distance_meters' => 'integer',
        'check_out_distance_meters' => 'integer',
        'is_late' => 'boolean',
        'late_minutes' => 'integer',
        'total_hours' => 'decimal:2',
        'is_manually_adjusted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    /**
     * Human-friendly status badge label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present' => $this->is_late ? 'Late' : 'On Time',
            'late' => 'Late',
            'half_day' => 'Half Day',
            'absent' => 'Absent',
            'leave' => 'On Leave',
            default => ucfirst((string) $this->status),
        };
    }

    /**
     * Status badge color class matching CRM styling.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present' => $this->is_late ? 'warning' : 'success',
            'late' => 'warning',
            'half_day' => 'info',
            'absent' => 'danger',
            'leave' => 'secondary',
            default => 'primary',
        };
    }
}
