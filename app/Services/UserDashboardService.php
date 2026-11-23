<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserDashboardService
{
    /**
     * Compute all 10 operational KPI metrics and task lists for a given user.
     */
    public function getUserMetrics(User $user, string $timeframe = 'today', ?string $customStart = null, ?string $customEnd = null): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($timeframe, $customStart, $customEnd);

        $userId = $user->id;

        // ----------------------------------------------------
        // 1. Base Query Scopes for this specific user
        // ----------------------------------------------------

        // Leads assigned to or created by this user
        $userLeadsQuery = Lead::query()->where(function (Builder $q) use ($userId) {
            $q->where('consultant_id', $userId)
              ->orWhere('created_by', $userId);
        });

        // Students assigned to or created by this user
        $userStudentsQuery = Student::query()->where(function (Builder $q) use ($userId) {
            $q->where('assigned_consultant_id', $userId)
              ->orWhere('assigned_marketing_id', $userId)
              ->orWhere('assigned_application_id', $userId)
              ->orWhere('created_by', $userId);
        });

        // Applications belonging to assigned students OR created by user
        $userApplicationsQuery = Application::query()->where(function (Builder $q) use ($userId) {
            $q->where('created_by', $userId)
              ->orWhereHas('student', function (Builder $sq) use ($userId) {
                  $sq->where('assigned_consultant_id', $userId)
                    ->orWhere('assigned_marketing_id', $userId)
                    ->orWhere('assigned_application_id', $userId)
                    ->orWhere('created_by', $userId);
              });
        });

        // ----------------------------------------------------
        // 2. Compute 10 Core Cards
        // ----------------------------------------------------

        // Card 1: New Leads (created in timeframe)
        $newLeadsCount = (clone $userLeadsQuery)
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->count();

        // Card 2: Calls Logged (leads with contact timestamp in timeframe OR student call activities)
        $leadCallsCount = (clone $userLeadsQuery)
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('last_contacted_at', [$startDate, $endDate]))
            ->count();
        $studentCallsCount = StudentActivity::where('user_id', $userId)
            ->where('type', 'call')
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->count();
        $totalCallsCount = $leadCallsCount + $studentCallsCount;

        // Card 3: Numbers Collected (leads or students with valid phone acquired in timeframe)
        $numbersFromLeads = (clone $userLeadsQuery)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->count();
        $numbersFromStudents = (clone $userStudentsQuery)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->count();
        $totalNumbersCollected = $numbersFromLeads + $numbersFromStudents;

        // Card 4: Follow-ups Due (scheduled for today or overdue)
        $followUpsDueCount = (clone $userLeadsQuery)
            ->whereNotNull('next_follow_up_at')
            ->where('status', '!=', 'rejected')
            ->when($timeframe === 'today', function ($q) {
                $q->whereDate('next_follow_up_at', '<=', now()->toDateString());
            }, function ($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereBetween('next_follow_up_at', [$startDate, $endDate]);
                }
            })
            ->count();

        // Card 5: Appointments Scheduled (Walk-in source or meeting activity)
        $appointmentsCount = (clone $userLeadsQuery)
            ->where('source', 'Walk-in')
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->count();
        $meetingActivitiesCount = StudentActivity::where('user_id', $userId)
            ->where('type', 'meeting')
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->count();
        $totalAppointments = $appointmentsCount + $meetingActivitiesCount;

        // Card 6: OL Pending (Offer Letter Pending)
        $olPendingCount = (clone $userApplicationsQuery)
            ->where(function (Builder $q) {
                $q->where(function ($sub) {
                    $sub->whereNull('offer_letter_received')->orWhere('offer_letter_received', false);
                })->whereIn('status', ['pending', 'applied', 'submitted', 'under_review'])
                ->orWhereHas('student', function ($sq) {
                    $sq->where('remarks', 'like', '%OL Pending%')
                       ->orWhere('remarks', 'like', '%Offer Letter Pending%')
                       ->orWhere('remarks', 'like', '%Waiting for OL%');
                });
            })
            ->count();

        // Card 7: Final Files / Submissions (VFS scheduled, embassy submission, or visa file submitted)
        $finalFilesCount = (clone $userApplicationsQuery)
            ->where(function (Builder $q) {
                $q->where('file_submission', true)
                  ->orWhere('vfs_appointment', true)
                  ->orWhereHas('student', function ($sq) {
                      $sq->where('remarks', 'like', '%Embassy Submission%')
                         ->orWhere('remarks', 'like', '%Submission Pending%')
                         ->orWhere('remarks', 'like', '%Visa Apply%');
                  });
            })
            ->count();

        // Card 8: Successfully Completed (Visa approved or Flight Done)
        $successfullyCompletedCount = (clone $userApplicationsQuery)
            ->where(function (Builder $q) {
                $q->whereIn('visa_status', ['approved', 'visa_approved'])
                  ->orWhereIn('final_status', ['approved', 'visa_approved'])
                  ->orWhereHas('student', function ($sq) {
                      $sq->where('remarks', 'like', '%Visa Approval%')
                         ->orWhere('remarks', 'like', '%Visa Approved%')
                         ->orWhere('remarks', 'like', '%Flight Done%');
                  });
            })
            ->count();

        // Card 9: Payments Collected by this user
        $paymentsQuery = Payment::where('collected_by', $userId)
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()]));
        $paymentsCount = (clone $paymentsQuery)->count();
        $paymentsTotalAmount = (clone $paymentsQuery)->where('payment_status', 'completed')->sum('amount');

        // Card 10: Active Assigned Students Portfolio
        $activeStudentsCount = (clone $userStudentsQuery)->count();

        // ----------------------------------------------------
        // 3. Action Queues (Urgent follow-ups & Recent Activities)
        // ----------------------------------------------------
        $todayFollowUps = (clone $userLeadsQuery)
            ->whereNotNull('next_follow_up_at')
            ->where('status', '!=', 'rejected')
            ->orderBy('next_follow_up_at', 'asc')
            ->limit(8)
            ->get();

        $recentActivities = StudentActivity::with(['student'])
            ->where('user_id', $userId)
            ->latest()
            ->limit(6)
            ->get();

        $pendingOlApplications = (clone $userApplicationsQuery)
            ->with(['student', 'university', 'course'])
            ->where(function ($sub) {
                $sub->whereNull('offer_letter_received')->orWhere('offer_letter_received', false);
            })
            ->whereIn('status', ['pending', 'applied'])
            ->latest()
            ->limit(6)
            ->get();

        return [
            'timeframe' => $timeframe,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'cards' => [
                'new_leads' => [
                    'title' => 'New Leads',
                    'count' => $newLeadsCount,
                    'subtitle' => 'Created in period',
                    'icon' => 'user-plus',
                    'color' => 'primary',
                    'route' => route('admin.marketing.leads.index', ['consultant_id' => $userId]),
                ],
                'new_calls' => [
                    'title' => 'Calls Logged',
                    'count' => $totalCallsCount,
                    'subtitle' => 'Call contacts recorded',
                    'icon' => 'phone-call',
                    'color' => 'info',
                    'route' => route('admin.marketing.leads.index'),
                ],
                'numbers_collected' => [
                    'title' => 'Numbers Collected',
                    'count' => $totalNumbersCollected,
                    'subtitle' => 'Valid contacts acquired',
                    'icon' => 'address-book',
                    'color' => 'success',
                    'route' => route('admin.marketing.leads.index'),
                ],
                'follow_ups' => [
                    'title' => 'Follow-ups Due',
                    'count' => $followUpsDueCount,
                    'subtitle' => 'Scheduled / Overdue',
                    'icon' => 'calendar-clock',
                    'color' => 'warning',
                    'route' => route('admin.marketing.leads.index', ['status' => 'pending']),
                ],
                'appointments' => [
                    'title' => 'Appointments',
                    'count' => $totalAppointments,
                    'subtitle' => 'Walk-ins & meetings',
                    'icon' => 'calendar-check',
                    'color' => 'secondary',
                    'route' => route('admin.marketing.leads.index', ['source' => 'Walk-in']),
                ],
                'ol_pending' => [
                    'title' => 'OL Pending',
                    'count' => $olPendingCount,
                    'subtitle' => 'Awaiting offer letters',
                    'icon' => 'file-clock',
                    'color' => 'amber',
                    'route' => route('admin.applications.index', ['status' => 'pending']),
                ],
                'final_files' => [
                    'title' => 'Final Files',
                    'count' => $finalFilesCount,
                    'subtitle' => 'VFS & Embassy ready',
                    'icon' => 'folder-check',
                    'color' => 'purple',
                    'route' => route('admin.applications.index'),
                ],
                'completed' => [
                    'title' => 'Successfully Completed',
                    'count' => $successfullyCompletedCount,
                    'subtitle' => 'Visas approved / flights',
                    'icon' => 'trophy',
                    'color' => 'emerald',
                    'route' => route('admin.applications.index', ['status' => 'approved']),
                ],
                'payments' => [
                    'title' => 'Payments Collected',
                    'count' => 'BDT ' . number_format($paymentsTotalAmount, 0),
                    'subtitle' => "{$paymentsCount} Txns collected",
                    'icon' => 'banknotes',
                    'color' => 'rose',
                    'route' => route('admin.payments.index'),
                ],
                'active_students' => [
                    'title' => 'My Students',
                    'count' => $activeStudentsCount,
                    'subtitle' => 'In my active portfolio',
                    'icon' => 'users-group',
                    'color' => 'blue',
                    'route' => route('admin.students.index'),
                ],
            ],
            'queues' => [
                'follow_ups' => $todayFollowUps,
                'activities' => $recentActivities,
                'pending_ol' => $pendingOlApplications,
            ]
        ];
    }

    /**
     * Resolve start and end dates from timeframe keyword.
     */
    private function resolveDateRange(string $timeframe, ?string $customStart = null, ?string $customEnd = null): array
    {
        return match ($timeframe) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'all_time' => [null, null],
            'custom' => [
                $customStart ? Carbon::parse($customStart)->startOfDay() : now()->startOfMonth(),
                $customEnd ? Carbon::parse($customEnd)->endOfDay() : now()->endOfDay(),
            ],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }
}
