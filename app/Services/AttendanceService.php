<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    /**
     * Calculate distance between two GPS coordinates using the Haversine formula (meters).
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    /**
     * Check whether a client IP is allowed.
     */
    public function isIpAllowed(string $clientIp, ?string $allowedIps): bool
    {
        if (empty(trim((string)$allowedIps))) {
            return true;
        }

        $list = preg_split('/[\r\n,]+/', $allowedIps);
        $cleanIp = trim($clientIp);

        foreach ($list as $allowed) {
            $allowed = trim($allowed);
            if (empty($allowed)) {
                continue;
            }
            if ($allowed === '*' || $allowed === $cleanIp) {
                return true;
            }
            // Localhost normalization
            if (($cleanIp === '127.0.0.1' || $cleanIp === '::1') && ($allowed === '127.0.0.1' || $allowed === '::1' || $allowed === 'localhost')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process staff Check-In.
     */
    public function recordCheckIn(User $user, array $data): Attendance
    {
        $today = now()->toDateString();

        $existing = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            throw ValidationException::withMessages([
                'attendance' => 'You have already checked in today at ' . $existing->check_in->format('h:i A') . '.',
            ]);
        }

        $settings = AttendanceSetting::getSettings();
        $distance = null;

        // 1. Geofence Verification
        if ($settings->enforce_geofence) {
            $lat = $data['latitude'] ?? null;
            $lng = $data['longitude'] ?? null;

            if ($lat === null || $lng === null) {
                throw ValidationException::withMessages([
                    'location' => 'Location coordinates are required for attendance. Please enable location access in your browser.',
                ]);
            }

            $distance = $this->calculateDistance(
                (float) $lat,
                (float) $lng,
                (float) $settings->office_latitude,
                (float) $settings->office_longitude
            );

            if ($distance > $settings->geofence_radius_meters) {
                throw ValidationException::withMessages([
                    'location' => "You are {$distance}m away from {$settings->office_name}. Allowed check-in radius is within {$settings->geofence_radius_meters}m.",
                ]);
            }
        }

        // 2. IP Verification
        $clientIp = $data['ip'] ?? request()->ip();
        if ($settings->enforce_ip) {
            if (!$this->isIpAllowed($clientIp, $settings->allowed_ips)) {
                throw ValidationException::withMessages([
                    'ip' => "Your network IP ({$clientIp}) is not authorized for office check-in.",
                ]);
            }
        }

        // 3. Shift Time & Status Calculation
        $now = now();
        $shiftStart = Carbon::parse($today . ' ' . $settings->shift_start_time);
        $graceEnd = $shiftStart->copy()->addMinutes($settings->late_grace_minutes);
        $halfDayCutoff = Carbon::parse($today . ' ' . $settings->half_day_cutoff_time);

        $status = 'present';
        $isLate = false;
        $lateMinutes = 0;

        if ($now->greaterThan($halfDayCutoff)) {
            $status = 'half_day';
            $isLate = true;
            $lateMinutes = (int) $shiftStart->diffInMinutes($now);
        } elseif ($now->greaterThan($graceEnd)) {
            $status = 'late';
            $isLate = true;
            $lateMinutes = (int) $shiftStart->diffInMinutes($now);
        }

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'check_in' => $now,
                'check_in_ip' => $clientIp,
                'check_in_lat' => $data['latitude'] ?? null,
                'check_in_lng' => $data['longitude'] ?? null,
                'check_in_distance_meters' => $distance,
                'status' => $status,
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes,
                'device_info' => substr((string) ($data['device_info'] ?? request()->userAgent()), 0, 255),
                'notes' => $data['notes'] ?? null,
            ]
        );

        return $attendance;
    }

    /**
     * Process staff Check-Out.
     */
    public function recordCheckOut(User $user, array $data): Attendance
    {
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            throw ValidationException::withMessages([
                'attendance' => 'You must check in first before checking out.',
            ]);
        }

        if ($attendance->check_out) {
            throw ValidationException::withMessages([
                'attendance' => 'You have already checked out today at ' . $attendance->check_out->format('h:i A') . '.',
            ]);
        }

        $settings = AttendanceSetting::getSettings();
        $now = now();
        $distance = null;

        if (isset($data['latitude'], $data['longitude'])) {
            $distance = $this->calculateDistance(
                (float) $data['latitude'],
                (float) $data['longitude'],
                (float) $settings->office_latitude,
                (float) $settings->office_longitude
            );
        }

        $clientIp = $data['ip'] ?? request()->ip();

        // Calculate hours worked
        $minutesWorked = $attendance->check_in->diffInMinutes($now);
        $totalHours = round($minutesWorked / 60, 2);

        // If worked less than minimum half-day threshold, mark as half day
        $status = $attendance->status;
        if ($totalHours < (float) $settings->min_hours_half_day && $status === 'present') {
            $status = 'half_day';
        }

        $attendance->update([
            'check_out' => $now,
            'check_out_ip' => $clientIp,
            'check_out_lat' => $data['latitude'] ?? null,
            'check_out_lng' => $data['longitude'] ?? null,
            'check_out_distance_meters' => $distance,
            'total_hours' => $totalHours,
            'status' => $status,
        ]);

        return $attendance;
    }

    /**
     * Calculate monthly attendance penalty & salary deduction for an employee.
     */
    public function calculateMonthlyDeductions(User $user, string $month, ?float $baseSalary = null): array
    {
        $settings = AttendanceSetting::getSettings();

        // Parse month
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $today = now();

        // In active month, only count up to today
        $evalEnd = $endOfMonth->isFuture() ? min($endOfMonth, $today) : $endOfMonth;

        // Get weekend days from settings
        $weekendDays = $settings->weekend_days ?? ['Friday', 'Saturday'];

        // Determine total working days in month
        $workingDays = 0;
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        foreach ($period as $date) {
            if (!in_array($date->format('l'), $weekendDays)) {
                $workingDays++;
            }
        }
        $workingDays = max($workingDays, 1);

        // Fetch all attendance records for this month
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $evalEnd->toDateString()])
            ->get()
            ->keyBy(fn($att) => $att->date->toDateString());

        $presentDays = 0;
        $lateDays = 0;
        $halfDays = 0;
        $absentDays = 0;

        // Evaluate each day up to evalEnd
        $evalPeriod = CarbonPeriod::create($startOfMonth, $evalEnd);
        foreach ($evalPeriod as $day) {
            $dateStr = $day->toDateString();
            $dayName = $day->format('l');

            // Skip weekends
            if (in_array($dayName, $weekendDays)) {
                continue;
            }

            if ($attendances->has($dateStr)) {
                $att = $attendances->get($dateStr);
                if ($att->status === 'leave') {
                    // Approved leave - not absent
                    continue;
                } elseif ($att->status === 'half_day') {
                    $halfDays++;
                    $presentDays++;
                } elseif ($att->status === 'late' || $att->is_late) {
                    $lateDays++;
                    $presentDays++;
                } else {
                    $presentDays++;
                }
            } else {
                // If past date and no record, counted as absent
                if ($day->isPast() && !$day->isToday()) {
                    $absentDays++;
                }
            }
        }

        // Determine Base Salary (fetch from latest Salary record if not passed)
        if ($baseSalary === null) {
            $latestSalary = Salary::where('user_id', $user->id)->latest('month')->first();
            $baseSalary = $latestSalary
                ? ($settings->salary_basis === 'gross_salary' ? (float) $latestSalary->gross_salary : (float) $latestSalary->basic_salary)
                : 25000.00; // sensible default
        }

        // Daily Rate Divisor
        $divisor = match ($settings->salary_day_divisor_mode) {
            'fixed_30_days' => 30,
            'calendar_days' => $startOfMonth->daysInMonth,
            default => $workingDays,
        };
        $divisor = max($divisor, 1);
        $dailyRate = $baseSalary / $divisor;

        // 1. Late Penalty Calculation
        $penalizedLateDays = max(0, $lateDays - (int) $settings->grace_late_days_per_month);
        $lateDeduction = 0.0;

        if ($settings->late_deduction_mode === 'quota' && $settings->late_quota_days > 0) {
            $deductionQuotas = (int) floor($penalizedLateDays / $settings->late_quota_days);
            $lateDeduction = $deductionQuotas * ($dailyRate * (float) $settings->late_quota_penalty_days);
        } elseif ($settings->late_deduction_mode === 'fixed_amount') {
            $lateDeduction = $penalizedLateDays * (float) $settings->late_fixed_amount;
        } elseif ($settings->late_deduction_mode === 'percentage') {
            $lateDeduction = $penalizedLateDays * ($dailyRate * 0.25);
        }

        // 2. Absence Penalty Calculation
        $absenceDeduction = $absentDays * ($dailyRate * (float) $settings->absence_deduction_rate);

        // 3. Half-Day Penalty Calculation
        $halfDayDeduction = $halfDays * ($dailyRate * (float) $settings->half_day_deduction_rate);

        // Total Attendance Deduction
        $totalDeduction = round($lateDeduction + $absenceDeduction + $halfDayDeduction, 2);

        $details = [
            'month' => $month,
            'working_days' => $workingDays,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'penalized_late_days' => $penalizedLateDays,
            'half_days' => $halfDays,
            'absent_days' => $absentDays,
            'daily_rate' => round($dailyRate, 2),
            'late_deduction' => round($lateDeduction, 2),
            'absence_deduction' => round($absenceDeduction, 2),
            'half_day_deduction' => round($halfDayDeduction, 2),
            'total_attendance_deduction' => $totalDeduction,
            'formula' => "Daily Rate: BDT " . round($dailyRate, 2) . " | Late: BDT " . round($lateDeduction, 2) . " ({$lateDays} days) | Absent: BDT " . round($absenceDeduction, 2) . " ({$absentDays} days) | Half-day: BDT " . round($halfDayDeduction, 2) . " ({$halfDays} days)",
        ];

        return $details;
    }
}
