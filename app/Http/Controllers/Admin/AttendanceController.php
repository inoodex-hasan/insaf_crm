<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function __construct(protected AttendanceService $attendanceService)
    {
    }

    /**
     * Staff Check-In action.
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $data = [
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'ip' => $request->ip(),
                'device_info' => $request->userAgent(),
                'notes' => $validated['notes'] ?? null,
            ];

            $attendance = $this->attendanceService->recordCheckIn($user, $data);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Checked in successfully at ' . $attendance->check_in->format('h:i A') . ' (' . $attendance->status_label . ').',
                    'attendance' => $attendance,
                ]);
            }

            return redirect()->back()->with('success', 'Checked in successfully at ' . $attendance->check_in->format('h:i A') . ' (' . $attendance->status_label . ').');
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?? 'Failed to check in.',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during check-in: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Staff Check-Out action.
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        try {
            $data = [
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'ip' => $request->ip(),
            ];

            $attendance = $this->attendanceService->recordCheckOut($user, $data);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Checked out successfully at ' . $attendance->check_out->format('h:i A') . ' (Total: ' . $attendance->total_hours . ' hrs).',
                    'attendance' => $attendance,
                ]);
            }

            return redirect()->back()->with('success', 'Checked out successfully at ' . $attendance->check_out->format('h:i A') . ' (Total: ' . $attendance->total_hours . ' hrs).');
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?? 'Failed to check out.',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during check-out: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Staff personal attendance history & monthly view.
     */
    public function myAttendance(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));

        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date', 'desc')
            ->get();

        $deductions = $this->attendanceService->calculateMonthlyDeductions($user, $month);
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->first();
        $attendanceSettings = AttendanceSetting::getSettings();

        return view('admin.attendance.my-attendance', compact('attendances', 'month', 'deductions', 'todayAttendance', 'attendanceSettings'));
    }

    /**
     * Super Admin: Daily Attendance Board / Logs.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $date = $request->get('date', now()->toDateString());
        $status = $request->get('status');
        $search = $request->get('search');
        $userId = $request->get('user_id');

        $query = Attendance::with(['user', 'adjuster'])
            ->where('date', $date)
            ->orderBy('check_in', 'asc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $attendances = $query->paginate(25)->withQueryString();

        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin')->orWhere('name', 'superadmin');
        })->orderBy('name')->get();

        // Summary counts for selected day
        $totalStaff = $users->count();
        $checkedInCount = Attendance::where('date', $date)->count();
        $counts = [
            'total_staff' => $totalStaff,
            'checked_in' => $checkedInCount,
            'present' => Attendance::where('date', $date)->where('status', 'present')->count(),
            'late' => Attendance::where('date', $date)->where('status', 'late')->count(),
            'half_day' => Attendance::where('date', $date)->where('status', 'half_day')->count(),
            'absent' => Attendance::where('date', $date)->where('status', 'absent')->count(),
            'not_checked_in' => max(0, $totalStaff - $checkedInCount),
        ];

        return view('admin.attendance.index', compact('attendances', 'date', 'status', 'search', 'userId', 'counts', 'users'));
    }

    /**
     * Super Admin: Attendance Settings view.
     */
    public function settings()
    {
        $this->authorizeAdmin();

        $settings = AttendanceSetting::getSettings();

        return view('admin.attendance.settings', compact('settings'));
    }

    /**
     * Super Admin: Update Attendance Settings.
     */
    public function updateSettings(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'office_name' => ['required', 'string', 'max:255'],
            'office_latitude' => ['required', 'numeric', 'between:-90,90'],
            'office_longitude' => ['required', 'numeric', 'between:-180,180'],
            'geofence_radius_meters' => ['required', 'integer', 'min:20', 'max:5000'],
            'enforce_geofence' => ['nullable', 'boolean'],
            'enforce_ip' => ['nullable', 'boolean'],
            'allowed_ips' => ['nullable', 'string'],
            'shift_start_time' => ['required', 'date_format:H:i'],
            'late_grace_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'half_day_cutoff_time' => ['required', 'date_format:H:i'],
            'shift_end_time' => ['required', 'date_format:H:i'],
            'min_hours_full_day' => ['required', 'numeric', 'min:1', 'max:24'],
            'min_hours_half_day' => ['required', 'numeric', 'min:1', 'max:12'],
            'weekend_days' => ['nullable', 'array'],

            // Dynamic Salary Deduction Settings
            'salary_basis' => ['required', 'in:basic_salary,gross_salary'],
            'salary_day_divisor_mode' => ['required', 'in:actual_working_days,fixed_30_days,calendar_days'],
            'late_deduction_mode' => ['required', 'in:quota,fixed_amount,percentage,disabled'],
            'late_quota_days' => ['required', 'integer', 'min:1', 'max:30'],
            'late_quota_penalty_days' => ['required', 'numeric', 'min:0.1', 'max:10'],
            'late_fixed_amount' => ['nullable', 'numeric', 'min:0'],
            'grace_late_days_per_month' => ['nullable', 'integer', 'min:0', 'max:30'],
            'absence_deduction_rate' => ['required', 'numeric', 'min:0', 'max:5'],
            'half_day_deduction_rate' => ['required', 'numeric', 'min:0', 'max:2'],
            'auto_apply_salary_deduction' => ['nullable', 'boolean'],
        ]);

        $settings = AttendanceSetting::getSettings();

        $settings->update([
            'office_name' => $validated['office_name'],
            'office_latitude' => $validated['office_latitude'],
            'office_longitude' => $validated['office_longitude'],
            'geofence_radius_meters' => $validated['geofence_radius_meters'],
            'enforce_geofence' => $request->boolean('enforce_geofence'),
            'enforce_ip' => $request->boolean('enforce_ip'),
            'allowed_ips' => $validated['allowed_ips'],
            'shift_start_time' => $validated['shift_start_time'] . ':00',
            'late_grace_minutes' => $validated['late_grace_minutes'],
            'half_day_cutoff_time' => $validated['half_day_cutoff_time'] . ':00',
            'shift_end_time' => $validated['shift_end_time'] . ':00',
            'min_hours_full_day' => $validated['min_hours_full_day'],
            'min_hours_half_day' => $validated['min_hours_half_day'],
            'weekend_days' => $validated['weekend_days'] ?? ['Friday', 'Saturday'],

            // Dynamic Salary Deductions
            'salary_basis' => $validated['salary_basis'],
            'salary_day_divisor_mode' => $validated['salary_day_divisor_mode'],
            'late_deduction_mode' => $validated['late_deduction_mode'],
            'late_quota_days' => $validated['late_quota_days'],
            'late_quota_penalty_days' => $validated['late_quota_penalty_days'],
            'late_fixed_amount' => $validated['late_fixed_amount'] ?? 0.00,
            'grace_late_days_per_month' => $validated['grace_late_days_per_month'] ?? 0,
            'absence_deduction_rate' => $validated['absence_deduction_rate'],
            'half_day_deduction_rate' => $validated['half_day_deduction_rate'],
            'auto_apply_salary_deduction' => $request->boolean('auto_apply_salary_deduction'),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.attendance.settings')
            ->with('success', 'Attendance & Salary Deduction Settings updated successfully.');
    }

    /**
     * Super Admin: Manual override / adjustment for a specific attendance record.
     */
    public function adjust(Request $request, Attendance $attendance)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'status' => ['required', 'in:present,late,half_day,absent,leave'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'adjustment_reason' => ['required', 'string', 'max:255'],
        ]);

        $dateStr = $attendance->date->toDateString();
        $checkIn = !empty($validated['check_in_time'])
            ? Carbon::parse($dateStr . ' ' . $validated['check_in_time'] . ':00')
            : $attendance->check_in;

        $checkOut = !empty($validated['check_out_time'])
            ? Carbon::parse($dateStr . ' ' . $validated['check_out_time'] . ':00')
            : $attendance->check_out;

        $totalHours = $attendance->total_hours;
        if ($checkIn && $checkOut) {
            $totalHours = round($checkIn->diffInMinutes($checkOut) / 60, 2);
        }

        $attendance->update([
            'status' => $validated['status'],
            'is_late' => $validated['status'] === 'late',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_hours' => $totalHours,
            'is_manually_adjusted' => true,
            'adjusted_by' => Auth::id(),
            'adjustment_reason' => $validated['adjustment_reason'],
        ]);

        return redirect()->back()->with('success', 'Attendance record adjusted successfully.');
    }

    /**
     * Super Admin: Record manual attendance for staff.
     */
    public function manualStore(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:present,late,half_day,absent,leave'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $dateStr = $validated['date'];
        $checkIn = !empty($validated['check_in_time'])
            ? Carbon::parse($dateStr . ' ' . $validated['check_in_time'] . ':00')
            : ($validated['status'] !== 'absent' && $validated['status'] !== 'leave' ? Carbon::parse($dateStr . ' 09:30:00') : null);

        $checkOut = !empty($validated['check_out_time'])
            ? Carbon::parse($dateStr . ' ' . $validated['check_out_time'] . ':00')
            : ($checkIn ? Carbon::parse($dateStr . ' 18:30:00') : null);

        $totalHours = 0.0;
        if ($checkIn && $checkOut) {
            $totalHours = round($checkIn->diffInMinutes($checkOut) / 60, 2);
        }

        Attendance::updateOrCreate(
            ['user_id' => $validated['user_id'], 'date' => $dateStr],
            [
                'status' => $validated['status'],
                'is_late' => $validated['status'] === 'late',
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'total_hours' => $totalHours,
                'is_manually_adjusted' => true,
                'adjusted_by' => Auth::id(),
                'adjustment_reason' => 'Manual entry by Super Admin: ' . ($validated['notes'] ?? ''),
                'notes' => $validated['notes'],
            ]
        );

        return redirect()->back()->with('success', 'Manual attendance recorded successfully.');
    }

    /**
     * Super Admin: Monthly Attendance & Salary Deduction Report.
     */
    public function report(Request $request)
    {
        $this->authorizeAdmin();

        $month = $request->get('month', now()->format('Y-m'));
        $settings = AttendanceSetting::getSettings();

        // Get all staff users (exclude super admin)
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin')->orWhere('name', 'superadmin');
        })->orderBy('name')->get();

        $reportData = [];
        foreach ($users as $staff) {
            $deductionData = $this->attendanceService->calculateMonthlyDeductions($staff, $month);
            $reportData[] = [
                'user' => $staff,
                'metrics' => $deductionData,
            ];
        }

        return view('admin.attendance.report', compact('reportData', 'month', 'settings'));
    }

    /**
     * Helper to restrict Admin / Super Admin operations.
     */
    protected function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (!$user || !$user->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized. Administrator or Super Admin access is required.');
        }
    }
}
