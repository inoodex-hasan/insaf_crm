<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Application, Attendance, AttendanceSetting, Country, Course, CourseIntake, Currency, Lead, Payment, Salary, Student, University, User};
use HasinHayder\Tyro\Models\{Privilege, Role};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Redirect digital marketing users to campaigns page
        if (auth()->check() && auth()->user()->hasRole('digital-marketing')) {
            return redirect()->route('admin.marketing.campaigns.index');
        }

        // ========================================================
        // 1. Application & Admission Pipeline Intelligence Metrics
        // ========================================================
        $dateRange = trim((string) $request->get('date_range'));
        $intakeFilter = trim((string) $request->get('intake'));
        $yearFilter = trim((string) $request->get('year'));
        $countryFilter = $request->get('country_id');

        $startDate = null;
        $endDate = null;
        if (!empty($dateRange)) {
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

        // Filter options for dropdowns
        $pipelineCountries = Country::where('status', '1')->orderBy('name')->get(['id', 'name']);
        $pipelineIntakes = CourseIntake::distinct()->whereNotNull('intake_name')->where('intake_name', '!=', '')->orderBy('intake_name')->pluck('intake_name');
        
        // Dynamic academic years (e.g. Next Year down to 3 years ago)
        $currentYear = (int) date('Y');
        $pipelineYears = range($currentYear + 1, $currentYear - 3);

        // Build base application query with active filters
        $pipelineBase = Application::query();

        if ($startDate && $endDate) {
            $pipelineBase->whereBetween('applications.created_at', [$startDate, $endDate]);
        } elseif (!empty($yearFilter)) {
            $pipelineBase->whereYear('applications.created_at', (int) $yearFilter);
        }

        if (!empty($countryFilter)) {
            $pipelineBase->where(function ($q) use ($countryFilter) {
                $q->whereHas('student', fn($sq) => $sq->where('country_id', $countryFilter))
                  ->orWhereHas('university', fn($uq) => $uq->where('country_id', $countryFilter));
            });
        }

        if (!empty($intakeFilter)) {
            $pipelineBase->where(function ($q) use ($intakeFilter) {
                $q->where('course_intake_id', $intakeFilter)
                  ->orWhereHas('intake', fn($iq) => $iq->where('intake_name', $intakeFilter));
            });
        }

        // Compute 8 KPI Pipeline Metrics
        $pipelineMetrics = [
            'all_applications' => (clone $pipelineBase)->count(),
            'offers' => (clone $pipelineBase)->where(function ($q) {
                $q->where('offer_letter_received', true)
                  ->orWhereIn('status', ['offer_received', 'conditional_offer', 'unconditional_offer', 'offer', 'accepted']);
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

        // Detailed Sub-Category Breakdown for Popover Dropdowns
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

        $activeCurrencies = Currency::active()->get();

        $userModel = config('tyro-dashboard.user_model', 'App\\Models\\User');

        $stats = [
            'total_users' => class_exists($userModel) ? $userModel::count() : 0,
            'total_roles' => class_exists(Role::class) ? Role::count() : 0,
            'total_privileges' => class_exists(Privilege::class) ? Privilege::count() : 0,
            'total_students' => Student::count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
            'total_invoices' => class_exists(\App\Models\Invoice::class) ? \App\Models\Invoice::count() : 0,
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('payment_status', 'pending')->count(),
            'total_revenue' => Payment::where('payment_status', 'completed')->sum('amount'),
            'total_leads' => Lead::count(),
            'total_countries' => Country::where('status', '1')->count(),
            'total_universities' => University::where('status', '1')->count(),
            'total_courses' => Course::where('status', '1')->count(),
            'total_course_intakes' => CourseIntake::where('status', '1')->count(),
            // Salary stats
            'total_salary_expense' => Salary::sum('net_salary'),
            'total_salary_paid' => Salary::where('payment_status', 'paid')->sum('paid_amount'),
            'total_salary_pending' => Salary::where('payment_status', 'pending')->sum('net_salary'),
            'total_salary_partial' => Salary::where('payment_status', 'partial')->sum(DB::raw('net_salary - paid_amount')),
        ];

        $leads = Lead::latest()->paginate(15);

        $students = Student::latest()->paginate(15);

        $applications = Application::latest()->paginate(15);

        $payments = Payment::latest()->paginate(15);

        $collectors = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['marketing', 'consultant', 'application', 'admin', 'super-admin'])
              ->orWhereIn('name', ['Marketing', 'Consultant', 'Application', 'Admin', 'Super Admin']);
        })->orderBy('name')->get(['id', 'name']);

        $user = auth()->user();
        $todayAttendance = $user ? Attendance::where('user_id', $user->id)->where('date', now()->toDateString())->first() : null;
        $attendanceSettings = AttendanceSetting::getSettings();

        return view('admin.dashboard', compact(
            'stats',
            'leads',
            'students',
            'applications',
            'payments',
            'collectors',
            'activeCurrencies',
            'todayAttendance',
            'attendanceSettings',
            'pipelineMetrics',
            'pipelineDetails',
            'pipelineCountries',
            'pipelineIntakes',
            'pipelineYears',
            'dateRange',
            'intakeFilter',
            'yearFilter',
            'countryFilter'
        ));
    }
}
