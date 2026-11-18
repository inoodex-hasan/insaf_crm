<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\AttendanceResource;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AttendanceApiController extends BaseApiController
{
    public function __construct(protected AttendanceService $attendanceService)
    {
    }

    /**
     * Staff Check-In API action.
     */
    public function checkIn(Request $request): JsonResponse
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
            $attendance->load('user');

            return $this->sendResponse(
                new AttendanceResource($attendance),
                'Checked in successfully at ' . $attendance->check_in->format('h:i A') . ' (' . $attendance->status_label . ').',
                201
            );
        } catch (ValidationException $e) {
            return $this->sendError(
                collect($e->errors())->flatten()->first() ?? 'Failed to check in.',
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return $this->sendError('An error occurred during check-in: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Staff Check-Out API action.
     */
    public function checkOut(Request $request): JsonResponse
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
            $attendance->load('user');

            return $this->sendResponse(
                new AttendanceResource($attendance),
                'Checked out successfully at ' . $attendance->check_out->format('h:i A') . ' (Total: ' . $attendance->total_hours . ' hrs).'
            );
        } catch (ValidationException $e) {
            return $this->sendError(
                collect($e->errors())->flatten()->first() ?? 'Failed to check out.',
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return $this->sendError('An error occurred during check-out: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Staff personal attendance history & monthly view.
     */
    public function myAttendance(Request $request): JsonResponse
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

        return $this->sendResponse([
            'month' => $month,
            'today' => $todayAttendance ? new AttendanceResource($todayAttendance) : null,
            'deductions_summary' => $deductions,
            'records' => AttendanceResource::collection($attendances),
        ], 'Personal attendance history retrieved successfully.');
    }

    /**
     * Daily Attendance Board / Logs (Super Admin & Admin).
     */
    public function index(Request $request): JsonResponse
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

        $perPage = (int) $request->get('per_page', 25);
        $attendances = $query->paginate($perPage);

        $totalStaff = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin')->orWhere('name', 'superadmin');
        })->count();

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

        return $this->sendPaginated(
            AttendanceResource::collection($attendances),
            'Daily attendance records retrieved successfully.',
            200,
            ['summary' => $counts]
        );
    }

    /**
     * Monthly Attendance & Salary Deduction Report.
     */
    public function report(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $month = $request->get('month', now()->format('Y-m'));

        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin')->orWhere('name', 'superadmin');
        })->orderBy('name')->get();

        $reportData = [];
        foreach ($users as $staff) {
            $deductionData = $this->attendanceService->calculateMonthlyDeductions($staff, $month);
            $reportData[] = [
                'user' => [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'email' => $staff->email,
                    'designation' => $staff->designation,
                ],
                'metrics' => $deductionData,
            ];
        }

        return $this->sendResponse([
            'month' => $month,
            'staff_reports' => $reportData,
        ], 'Monthly attendance and deduction report retrieved successfully.');
    }

    /**
     * Get Attendance & Geofence Settings.
     */
    public function settings(): JsonResponse
    {
        $settings = AttendanceSetting::getSettings();

        return $this->sendResponse([
            'office_name' => $settings->office_name,
            'office_latitude' => (float) $settings->office_latitude,
            'office_longitude' => (float) $settings->office_longitude,
            'geofence_radius_meters' => $settings->geofence_radius_meters,
            'office_locations' => $settings->office_locations,
            'enforce_geofence' => (bool) $settings->enforce_geofence,
            'shift_start_time' => $settings->shift_start_time,
            'shift_end_time' => $settings->shift_end_time,
            'late_grace_minutes' => $settings->late_grace_minutes,
            'half_day_cutoff_time' => $settings->half_day_cutoff_time,
            'weekend_days' => $settings->weekend_days,
        ], 'Attendance settings retrieved successfully.');
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
