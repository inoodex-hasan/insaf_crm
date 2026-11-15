<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use Illuminate\Database\Seeder;

class AttendanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttendanceSetting::firstOrCreate([], [
            'office_name' => 'Insaf Educancy Main Office',
            'office_latitude' => 23.79250000,
            'office_longitude' => 90.40780000,
            'geofence_radius_meters' => 150,
            'enforce_geofence' => true,
            'enforce_ip' => false,
            'allowed_ips' => "127.0.0.1\n::1",
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
}
