<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\{Application, Attendance, Country, Course, CourseIntake, Currency, Invoice, Lead, Payment, Student, University, User};
use App\Services\UserDashboardService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends BaseApiController
{
    /**
     * Return comprehensive pipeline intelligence metrics and general CRM statistics for mobile apps.
     */
    public function index(Request $request): JsonResponse
    {
        $dateRange = trim((string) $request->get('date_range'));
        $intakeFilter = trim((string) $request->get('intake'));
        $yearFilter = trim((string) $request->get('year'));
        $countryFilter = $request->get('country_id');

        $startDate = null;
        $endDate = null;

        // Support explicit start_date and end_date params from mobile date pickers
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
            } catch (\Throwable $e) {
                $startDate = null;
                $endDate = null;
            }
        } elseif (!empty($dateRange)) {
            $parts = preg_split('/\s+(?:to|-)\s+/i', $dateRange);
            if (count($parts) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($parts[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($parts[1]))->endOfDay();
                } catch (\Throwable $e) {
                    try {
                        $startDate = Carbon::parse(trim($parts[0]))->startOfDay();
                        $endDate = Carbon::parse(trim($parts[1]))->endOfDay();
                    } catch (\Throwable $e2) {
                        $startDate = null;
                        $endDate = null;
                    }
                }
            }
        }

        // Base Pipeline Query
        $pipelineBase = Application::query();

        if ($startDate && $endDate) {
            $pipelineBase->whereBetween('created_at', [$startDate, $endDate]);
        }

        if (!empty($intakeFilter)) {
            $pipelineBase->where(function ($q) use ($intakeFilter) {
                $q->whereHas('intake', fn($sub) => $sub->where('intake_name', 'like', "%{$intakeFilter}%"))
                  ->orWhere('intake', 'like', "%{$intakeFilter}%");
            });
        }

        if (!empty($yearFilter)) {
            $pipelineBase->where(function ($q) use ($yearFilter) {
                $q->whereHas('intake', fn($sub) => $sub->where('year', $yearFilter))
                  ->orWhereYear('created_at', $yearFilter);
            });
        }

        if (!empty($countryFilter)) {
            $pipelineBase->where(function ($q) use ($countryFilter) {
                $q->where('country_id', $countryFilter)
                  ->orWhereHas('university', fn($sub) => $sub->where('country_id', $countryFilter));
            });
        }

        // 8 Pipeline Metrics matching the web dashboard
        $pipelineMetrics = [
            'all_applications' => (clone $pipelineBase)->count(),
            'offers' => (clone $pipelineBase)->where(function ($q) {
                $q->where('offer_letter_received', true)
                  ->orWhereIn('status', ['offer_received', 'conditional_offer', 'unconditional_offer', 'accepted', 'offer']);
            })->count(),
            'payments' => (clone $pipelineBase)->where(function ($q) {
                $q->where('final_payment_status', true)
                  ->orWhere('tuition_fee_status', 'paid')
                  ->orWhere('security_deposit_status', true)
                  ->orWhere('cvu_fee_status', true)
                  ->orWhere('admission_fee_status', true)
                  ->orWhereHas('payments', fn($p) => $p->where('payment_status', 'completed')->orWhere('amount', '>', 0));
            })->count(),
            'visa_received' => (clone $pipelineBase)->where(function ($q) {
                $q->whereIn('visa_status', ['approved', 'issued', 'granted', 'visa_approved'])
                  ->orWhereIn('final_status', ['visa_approved', 'approved'])
                  ->orWhere('vfs_result', 'approved');
            })->count(),
            'visa_rejected' => (clone $pipelineBase)->where(function ($q) {
                $q->whereIn('visa_status', ['rejected', 'refused', 'visa_rejected'])
                  ->orWhereIn('final_status', ['visa_rejected', 'rejected'])
                  ->orWhereIn('vfs_result', ['rejected', 'refused']);
            })->count(),
            'non_enrolment' => (clone $pipelineBase)->where(function ($q) {
                $q->whereIn('status', ['withdrawn', 'cancelled', 'non_enrolment', 'dropped'])
                  ->orWhereIn('final_status', ['withdrawn', 'cancelled', 'non_enrolment', 'dropout']);
            })->count(),
            'deferrals' => (clone $pipelineBase)->where(function ($q) {
                $q->whereIn('status', ['deferred', 'deferral'])
                  ->orWhereIn('final_status', ['deferred', 'deferral']);
            })->count(),
            'pending_partner' => (clone $pipelineBase)->where(function ($q) {
                $q->whereIn('status', ['pending', 'applied', 'submitted', 'under_review', 'partner_review', 'waiting'])
                  ->where(function ($sub) {
                      $sub->whereNull('offer_letter_received')->orWhere('offer_letter_received', false);
                  });
            })->count(),
        ];

        // Detailed Sub-Category Breakdown for Popover / Accordions in mobile
        $pipelineDetails = [
            'applications' => [
                'total' => $pipelineMetrics['all_applications'],
                'pending' => (clone $pipelineBase)->where('status', 'pending')->count(),
                'applied' => (clone $pipelineBase)->whereIn('status', ['applied', 'submitted', 'under_review'])->count(),
                'cancelled' => (clone $pipelineBase)->whereIn('status', ['cancelled', 'withdrawn'])->count(),
                'rejected' => (clone $pipelineBase)->where('status', 'rejected')->count(),
            ],
            'offers' => [
                'total' => $pipelineMetrics['offers'],
                'received' => (clone $pipelineBase)->where(function ($q) {
                    $q->where('offer_letter_received', true)
                      ->orWhereIn('status', ['offer_received', 'accepted', 'offer']);
                })->count(),
                'conditional' => (clone $pipelineBase)->where('status', 'conditional_offer')->count(),
                'unconditional' => (clone $pipelineBase)->where('status', 'unconditional_offer')->count(),
                'waiting' => (clone $pipelineBase)->where(function ($q) {
                    $q->whereNull('offer_letter_received')->orWhere('offer_letter_received', false);
                })->whereIn('status', ['pending', 'applied'])->count(),
            ],
        ];

        // Filter Options for Mobile Filter Sheet
        $pipelineCountries = Country::where('status', '1')->orderBy('name')->get(['id', 'name', 'code']);

        $pipelineIntakes = CourseIntake::select('intake_name')
            ->distinct()
            ->whereNotNull('intake_name')
            ->where('intake_name', '!=', '')
            ->orderBy('intake_name')
            ->pluck('intake_name')
            ->toArray();
        if (empty($pipelineIntakes)) {
            $pipelineIntakes = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        }

        $currentYear = (int) date('Y');
        $pipelineYears = range($currentYear - 2, $currentYear + 2);

        // Overall General Statistics
        $user = $request->user();
        $todayAttendance = $user ? Attendance::where('user_id', $user->id)->where('date', now()->toDateString())->first() : null;

        $generalStats = [
            'total_students' => Student::count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
            'total_leads' => Lead::count(),
            'total_countries' => Country::where('status', '1')->count(),
            'total_universities' => University::where('status', '1')->count(),
            'total_courses' => Course::where('status', '1')->count(),
            'total_course_intakes' => CourseIntake::where('status', '1')->count(),
            'total_invoices' => class_exists(Invoice::class) ? Invoice::count() : 0,
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('payment_status', 'pending')->count(),
            'total_revenue' => (float) Payment::where('payment_status', 'completed')->sum('amount'),
        ];

        // Individual User Operations (Daily Doings, 10 Operational Cards & Action Queues)
        $timeframe = $request->get('timeframe', 'today');
        $targetUser = $user;
        $isInspectingOther = false;

        $isAdmin = $user && ($user->isAdminOrSuperAdmin() || $user->hasRole('admin') || $user->hasRole('super-admin') || $user->hasRole('superadmin'));
        if ($isAdmin && $request->filled('user_id')) {
            $foundUser = User::find($request->get('user_id'));
            if ($foundUser) {
                $targetUser = $foundUser;
                $isInspectingOther = ($foundUser->id !== $user->id);
            }
        }

        $userDashboardService = app(UserDashboardService::class);
        $userOperations = $targetUser ? $userDashboardService->getUserMetrics($targetUser, $timeframe, $request->get('start_date'), $request->get('end_date')) : null;

        return $this->sendResponse([
            'user_operations' => $userOperations ? [
                'viewing_user' => [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                    'designation' => $targetUser->designation ?? null,
                ],
                'is_inspecting_other' => $isInspectingOther,
                'timeframe' => $timeframe,
                'cards' => $userOperations['cards'] ?? [],
                'queues' => [
                    'follow_ups' => $userOperations['queues']['follow_ups'] ?? [],
                    'pending_ol' => $userOperations['queues']['pending_ol'] ?? [],
                    'activities' => $userOperations['queues']['activities'] ?? [],
                ],
            ] : null,
            'pipeline_metrics' => $pipelineMetrics,
            'pipeline_details' => $pipelineDetails,
            'general_stats' => $generalStats,
            'filter_options' => [
                'countries' => $pipelineCountries,
                'intakes' => $pipelineIntakes,
                'years' => $pipelineYears,
                'timeframes' => ['today', 'yesterday', 'this_week', 'this_month', 'all_time'],
            ],
            'applied_filters' => [
                'start_date' => $startDate ? $startDate->toDateString() : null,
                'end_date' => $endDate ? $endDate->toDateString() : null,
                'intake' => $intakeFilter ?: null,
                'year' => $yearFilter ?: null,
                'country_id' => $countryFilter ?: null,
                'timeframe' => $timeframe,
                'user_id' => $targetUser ? $targetUser->id : null,
            ],
            'attendance_status' => $todayAttendance ? [
                'checked_in' => !empty($todayAttendance->check_in),
                'check_in_time' => $todayAttendance->check_in,
                'checked_out' => !empty($todayAttendance->check_out),
                'check_out_time' => $todayAttendance->check_out,
                'status' => $todayAttendance->status,
            ] : null,
        ], 'Dashboard metrics retrieved successfully.');
    }
}
