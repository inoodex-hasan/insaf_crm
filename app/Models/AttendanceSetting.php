<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $table = 'attendance_settings';

    protected $fillable = [
        'office_name',
        'office_latitude',
        'office_longitude',
        'geofence_radius_meters',
        'office_locations',
        'enforce_geofence',
        'enforce_ip',
        'allowed_ips',
        'shift_start_time',
        'late_grace_minutes',
        'half_day_cutoff_time',
        'shift_end_time',
        'min_hours_full_day',
        'min_hours_half_day',
        'weekend_days',
        'salary_basis',
        'salary_day_divisor_mode',
        'late_deduction_mode',
        'late_quota_days',
        'late_quota_penalty_days',
        'late_fixed_amount',
        'grace_late_days_per_month',
        'absence_deduction_rate',
        'half_day_deduction_rate',
        'auto_apply_salary_deduction',
        'updated_by',
    ];

    protected $casts = [
        'office_latitude' => 'decimal:8',
        'office_longitude' => 'decimal:8',
        'geofence_radius_meters' => 'integer',
        'office_locations' => 'array',
        'enforce_geofence' => 'boolean',
        'enforce_ip' => 'boolean',
        'late_grace_minutes' => 'integer',
        'min_hours_full_day' => 'decimal:2',
        'min_hours_half_day' => 'decimal:2',
        'weekend_days' => 'array',
        'late_quota_days' => 'integer',
        'late_quota_penalty_days' => 'decimal:2',
        'late_fixed_amount' => 'decimal:2',
        'grace_late_days_per_month' => 'integer',
        'absence_deduction_rate' => 'decimal:2',
        'half_day_deduction_rate' => 'decimal:2',
        'auto_apply_salary_deduction' => 'boolean',
    ];

    /**
     * Get the singleton attendance settings record, or create default.
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'office_name' => 'Insaf Educancy Main Office',
            'office_latitude' => 23.79250000,
            'office_longitude' => 90.40780000,
            'geofence_radius_meters' => 150,
            'enforce_geofence' => true,
            'enforce_ip' => false,
            'allowed_ips' => '127.0.0.1',
            'shift_start_time' => '09:30:00',
            'late_grace_minutes' => 15,
            'half_day_cutoff_time' => '13:30:00',
            'shift_end_time' => '18:30:00',
            'min_hours_full_day' => 8.00,
            'min_hours_half_day' => 4.00,
            'weekend_days' => ['Friday', 'Saturday'],
            'salary_basis' => 'basic_salary',
            'salary_day_divisor_mode' => 'actual_working_days',
            'late_deduction_mode' => 'quota',
            'late_quota_days' => 3,
            'late_quota_penalty_days' => 1.00,
            'late_fixed_amount' => 0.00,
            'grace_late_days_per_month' => 0,
            'absence_deduction_rate' => 1.00,
            'half_day_deduction_rate' => 0.50,
            'auto_apply_salary_deduction' => true,
        ]);
    }

    /**
     * Get all office locations (supports multi-office branch geofencing).
     *
     * @return array<int, array{name: string, latitude: float, longitude: float, radius_meters: int}>
     */
    public function getAllOffices(): array
    {
        if (!empty($this->office_locations) && is_array($this->office_locations) && count($this->office_locations) > 0) {
            return array_values(array_filter($this->office_locations, function ($loc) {
                return !empty($loc['name']) && isset($loc['latitude']) && isset($loc['longitude']);
            }));
        }

        return [
            [
                'name' => $this->office_name ?: 'Main Office',
                'latitude' => (float) $this->office_latitude,
                'longitude' => (float) $this->office_longitude,
                'radius_meters' => (int) ($this->geofence_radius_meters ?: 100),
            ]
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
