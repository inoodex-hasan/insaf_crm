<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Models\{Application, Country, Course, CourseIntake, Student, University, User};
use App\Notifications\NewApplicationNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['student', 'university', 'course', 'intake', 'creator']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('application_id', 'like', "%{$search}%")
                    ->orWhereHas(
                        'student',
                        function ($sq) use ($search) {
                            $sq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        }
                    );
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Offer Letter filter
        if ($offerLetter = $request->get('offer_letter')) {
            $query->where('offer_letter_received', $offerLetter === '1');
        }

        // Visa Status filter
        if ($visaStatus = $request->get('visa_status')) {
            $query->where('visa_status', $visaStatus);
        }

        // EMGS Score filter
        if ($emgsScore = $request->get('emgs_score')) {
            $query->where('emgs_score', $emgsScore);
        }

        // Security Deposit filter
        if ($securityDeposit = $request->get('security_deposit')) {
            $query->where('security_deposit_status', $securityDeposit === '1');
        }

        // CVU Fee filter
        if ($cvuFee = $request->get('cvu_fee')) {
            $query->where('cvu_fee_status', $cvuFee === '1');
        }

        // Final Payment filter
        if ($finalPayment = $request->get('final_payment')) {
            $query->where('final_payment_status', $finalPayment === '1');
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        return view('admin.applications.index', compact('applications'));
    }

    public function create(Request $request)
    {
        $students = Student::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email', 'phone', 'passport_number']);
        $countries = Country::where('status', '1')->orderBy('name')->get(['id', 'name']);
        $universities = University::where('status', '1')->orderBy('name')->get(['id', 'name']);

        $selected_student = $request->get('student_id');

        return view('admin.applications.create', compact('students', 'universities', 'selected_student', 'countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'university_id' => 'nullable|exists:universities,id',
            'course_id' => 'nullable|exists:courses,id',
            'course_intake_id' => 'nullable|exists:course_intakes,id',
            // 'tuition_fee' => 'required|numeric|min:0',
            // 'total_fee' => 'required|numeric|min:0',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'offer_letter_received' => 'nullable|boolean',
            'vfs_appointment' => 'nullable|boolean',
            'file_submission' => 'nullable|boolean',
            'visa_status' => 'nullable|in:not_applied,pending,approved,rejected',
            'tuition_fee_status' => 'nullable|in:pending,paid,partial',
            'service_charge_status' => 'nullable|in:pending,paid,partial',
            'application_priority' => 'nullable|in:normal,priority,vip',
            'internal_notes' => 'nullable|string',
            'documents_checklist' => 'nullable|array',
            'final_status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'security_deposit_status' => 'nullable|boolean',
            'cvu_fee_status' => 'nullable|boolean',
            'admission_fee_status' => 'nullable|boolean',
            'final_payment_status' => 'nullable|boolean',
            'emgs_payment_status' => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();

        $application = Application::create($validated);

        // Notify Application & Operational Team
        $applicationTeam = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['application', 'consultant', 'marketing'])
              ->orWhereIn('name', ['Application', 'Consultant', 'Marketing']);
        })->get();

        if ($applicationTeam->count() > 0) {
            Notification::send($applicationTeam, new NewApplicationNotification($application));
        }

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Application created successfully.');
    }

    public function show(Application $application)
    {
        $application->load(['student', 'university', 'university.country', 'course', 'intake', 'creator', 'payments.collector', 'commissions.user', 'documents.creator']);

        return view('admin.applications.show', compact('application'));
    }

    public function edit(Application $application)
    {
        $application->load('payments.collector');
        $students = Student::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $countries = Country::where('status', '1')->orderBy('name')->get(['id', 'name']);
        $universities = University::where('status', '1')->orderBy('name')->get(['id', 'name']);

        $universityId = old('university_id', $application->university_id);
        $courseId = old('course_id', $application->course_id);

        $courses = [];
        if ($universityId) {
            $courses = Course::where('university_id', $universityId)
                ->get(['courses.id', 'courses.name', 'courses.tuition_fee']);
        }

        $intakes = [];
        if ($courseId) {
            $intakes = CourseIntake::where('course_id', $courseId)->get(['id', 'intake_name']);
        }

        return view('admin.applications.edit', compact('application', 'students', 'universities', 'courses', 'intakes', 'countries'));
    }

    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'university_id' => 'nullable|exists:universities,id',
            'course_id' => 'nullable|exists:courses,id',
            'course_intake_id' => 'nullable|exists:course_intakes,id',
            // 'tuition_fee' => 'required|numeric|min:0',
            // 'total_fee' => 'required|numeric|min:0',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'offer_letter_received' => 'nullable|boolean',
            'vfs_appointment' => 'nullable|boolean',
            'vfs_appointment_date' => 'nullable|date',
            'file_submission' => 'nullable|boolean',
            'file_submission_date' => 'nullable|date',
            'visa_status' => 'nullable|in:not_applied,pending,approved,rejected',
            'visa_decision_date' => 'nullable|date',
            'tuition_fee_status' => 'nullable|in:pending,paid,partial',
            'service_charge_status' => 'nullable|in:pending,paid,partial',
            'application_priority' => 'nullable|in:normal,priority,vip',
            'internal_notes' => 'nullable|string',
            'documents_checklist' => 'nullable|array',
            'final_status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'security_deposit_status' => 'nullable|boolean',
            'cvu_fee_status' => 'nullable|boolean',
            'admission_fee_status' => 'nullable|boolean',
            'final_payment_status' => 'nullable|boolean',
            'emgs_payment_status' => 'nullable|boolean',
            'emgs_score' => 'nullable|integer|min:0|max:100',
        ]);

        $application->update($validated);

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Application updated successfully.');
    }

    public function destroy(Application $application)
    {
        $application->delete();

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Application deleted successfully.');
    }

    // public function downloadPdf(Application $application)
    // {
    //     $application->load(['student', 'university', 'university.country', 'course', 'intake']);

    //     $pdf = Pdf::loadView('admin.applications.pdf', compact('application'));

    //     return $pdf->download('Application_' . $application->application_id . '.pdf');
    // }

public function downloadPdf(Application $application)
{
    $application->load(['student', 'university', 'university.country', 'course', 'intake']);

    $mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_top' => 10,
    'margin_bottom' => 10,
    ]);

    $html = view('admin.applications.pdf', compact('application'))->render();

    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'S'))
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="Application_' . $application->application_id . '.pdf"');
}

    public function getUniversities(Request $request)
    {
        $query = University::where('status', '1');
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        $universities = $query->orderBy('name')->get(['id', 'name']);
        return response()->json($universities);
    }

    public function getCourses(Request $request)
    {
        $query = Course::query();

        if ($request->has('university_id') && $request->university_id) {
            $query->where('university_id', $request->university_id);
        }

        $courses = $query->orderBy('courses.name')
            ->get(['courses.id', 'courses.name', 'courses.tuition_fee']);
        return response()->json($courses);
    }

    public function getIntakes(Request $request)
    {
        $intakes = CourseIntake::where('course_id', $request->course_id)->orderBy('intake_name')->get(['id', 'intake_name']);
        return response()->json($intakes);
    }

    public function getStudentDetails(Request $request)
    {
        $request->validate(['student_id' => 'required|exists:students,id']);
        $student = Student::with(['university', 'course', 'intake'])->findOrFail($request->student_id);

        return response()->json([
            'country_id' => $student->country_id,
            'university_id' => $student->university_id,
            'course_id' => $student->course_id,
            'course_intake_id' => $student->course_intake_id,
        ]);
    }

    public function invoice(Application $application)
    {
        $application->load(['student', 'university', 'course', 'intake', 'payments']);

        return view('admin.applications.invoice', compact('application'));
    }

    public function invoiceData(Application $application)
    {
        $application->load(['student', 'university', 'course', 'intake']);

        return response()->json([
            'id' => $application->id,
            'application_id' => $application->application_id,
            'student_id' => $application->student_id,
            'university' => $application->university->name ?? '-',
            'course' => $application->course->name ?? '-',
            'intake' => $application->intake->intake_name ?? '-',
            'tuition_fee' => number_format($application->tuition_fee ?? 0, 2, '.', ''),
            'service_charge' => number_format($application->service_charge ?? 0, 2, '.', ''),
        ]);
    }

    /**
     * Country-wise & University-wise Student Applications Report
     */
    public function countryReport(Request $request)
    {
        $query = Application::with(['student', 'university.country', 'course', 'intake', 'creator']);

        // Filter by Date Range (created_at)
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by Country
        if ($request->filled('country_id')) {
            $query->whereHas('university', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        // Filter by University
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        }

        // Filter by Application Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Visa Status
        if ($request->filled('visa_status')) {
            $query->where('visa_status', $request->visa_status);
        }

        // Search by student name, passport, application ID, or university name
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('application_id', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('passport_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('university', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('short_name', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->latest()->get();

        // High-level KPIs
        $totalApplications = $applications->count();
        $uniqueStudents = $applications->pluck('student_id')->filter()->unique()->count();

        // Group by Country
        $countryGroups = $applications->groupBy(function ($app) {
            return $app->university && $app->university->country 
                ? $app->university->country->name 
                : 'Unassigned Country';
        })->map(function ($countryApps, $countryName) use ($totalApplications) {
            // Group by University under Country
            $universityGroups = $countryApps->groupBy(function ($app) {
                return $app->university ? $app->university->name : 'Unassigned University';
            })->map(function ($uniApps, $uniName) {
                $firstApp = $uniApps->first();
                $university = $firstApp->university ?? null;

                return [
                    'university_id' => $university ? $university->id : null,
                    'university_name' => $uniName,
                    'short_name' => $university ? $university->short_name : null,
                    'total_applications' => $uniApps->count(),
                    'unique_students' => $uniApps->pluck('student_id')->filter()->unique()->count(),
                    'pending_count' => $uniApps->where('status', 'pending')->count(),
                    'approved_count' => $uniApps->where('status', 'approved')->count(),
                    'offer_letter_count' => $uniApps->where('offer_letter_received', true)->count(),
                    'visa_approved_count' => $uniApps->where('visa_status', 'approved')->count(),
                    'applications' => $uniApps,
                ];
            })->sortByDesc('total_applications');

            $firstApp = $countryApps->first();
            $country = $firstApp->university->country ?? null;

            return [
                'country_id' => $country ? $country->id : null,
                'country_name' => $countryName,
                'total_applications' => $countryApps->count(),
                'unique_students' => $countryApps->pluck('student_id')->filter()->unique()->count(),
                'universities_count' => $universityGroups->count(),
                'percentage' => $totalApplications > 0 ? round(($countryApps->count() / $totalApplications) * 100, 1) : 0,
                'universities' => $universityGroups,
            ];
        })->sortByDesc('total_applications');

        $totalCountries = $countryGroups->filter(fn($c) => $c['country_name'] !== 'Unassigned Country')->count();
        $totalUniversities = $applications->pluck('university_id')->filter()->unique()->count();

        // Filter dropdown lists
        $filterCountries = Country::orderBy('name')->get(['id', 'name']);
        $filterUniversities = University::orderBy('name')->get(['id', 'name', 'country_id']);

        return view('admin.applications.country_report', compact(
            'countryGroups',
            'totalApplications',
            'uniqueStudents',
            'totalCountries',
            'totalUniversities',
            'filterCountries',
            'filterUniversities'
        ));
    }
}
