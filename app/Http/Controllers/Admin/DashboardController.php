<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Application, Country, Course, CourseIntake, Currency, Lead, Payment, Salary, Student, University};
use HasinHayder\Tyro\Models\{Privilege, Role};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class DashboardController extends Controller
{
    public function index()
    {
        // Redirect digital marketing users to campaigns page
        if (auth()->check() && auth()->user()->hasRole('digital-marketing')) {
            return redirect()->route('admin.marketing.campaigns.index');
        }

        $activeCurrencies = Currency::active()->get();

        $userModel = config('tyro-dashboard.user_model', 'App\\Models\\User');

        $stats = [
            'total_users' => class_exists($userModel) ? $userModel::count() : 0,
            'total_roles' => class_exists(Role::class) ? Role::count() : 0,
            'total_privileges' => class_exists(Privilege::class) ? Privilege::count() : 0,
            'total_students' => Student::count(),
            'total_applications' => Application::count(),
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

        return view('admin.dashboard', compact('stats', 'leads', 'students', 'applications', 'payments', 'activeCurrencies'));
    }
}
