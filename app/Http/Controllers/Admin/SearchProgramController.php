<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Country;
use App\Models\University;
use App\Models\CostingTemplate;
use App\Models\Student;
use App\Models\StudentCosting;
use App\Models\StudentActivity;
use Illuminate\Http\Request;

class SearchProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['university.country'])->where('status', 1);

        // Keyword Search (Program title, university name, discipline)
        if ($request->filled('keyword')) {
            $keyword = trim($request->input('keyword'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('discipline_area', 'like', "%{$keyword}%")
                  ->orWhereHas('university', function ($uq) use ($keyword) {
                      $uq->where('name', 'like', "%{$keyword}%")
                         ->orWhere('short_name', 'like', "%{$keyword}%");
                  });
            });
        }

        // Intake Month Filter
        if ($request->filled('intake_month') && $request->input('intake_month') !== 'All') {
            $month = $request->input('intake_month');
            $query->where(function ($q) use ($month) {
                $q->whereJsonContains('available_intake_months', $month)
                  ->orWhere('available_intake_months', 'like', '%"' . $month . '"%');
            });
        }

        // Intake Year Filter
        if ($request->filled('intake_year') && $request->input('intake_year') !== 'All') {
            $year = (string) $request->input('intake_year');
            $query->where(function ($q) use ($year) {
                $q->whereJsonContains('intake_years', $year)
                  ->orWhere('intake_years', 'like', '%"' . $year . '"%');
            });
        }

        // University Destination Country
        if ($request->filled('country_id')) {
            $countryId = $request->input('country_id');
            $query->whereHas('university', function ($uq) use ($countryId) {
                $uq->where('country_id', $countryId);
            });
        }

        // University Province / State
        if ($request->filled('state_province')) {
            $stateProvince = $request->input('state_province');
            $query->whereHas('university', function ($uq) use ($stateProvince) {
                $uq->where('state_province', $stateProvince);
            });
        }

        // Program Levels (Array)
        if ($request->filled('program_levels') && is_array($request->input('program_levels'))) {
            $query->whereIn('program_level', $request->input('program_levels'));
        }

        // Study Area (Faculty)
        if ($request->filled('study_area')) {
            $query->where('study_area', $request->input('study_area'));
        }

        // Discipline Area (Major)
        if ($request->filled('discipline_area')) {
            $query->where('discipline_area', $request->input('discipline_area'));
        }

        // Duration Filter
        if ($request->filled('duration_type')) {
            $query->where('duration_type', $request->input('duration_type'));
        }

        // ESL / ELP Available
        if ($request->boolean('esl_available')) {
            $query->where('esl_available', 1);
        }

        // STEM Program
        if ($request->boolean('stem_program')) {
            $query->where('stem_program', 1);
        }

        // Requirements Checklist (e.g. ielts, toefl_ibt, pte, without_english, etc.)
        if ($request->filled('requirements') && is_array($request->input('requirements'))) {
            foreach ($request->input('requirements') as $req) {
                $query->where(function ($q) use ($req) {
                    $q->whereJsonContains('requirements', $req)
                      ->orWhere('requirements', 'like', '%"' . $req . '"%');
                });
            }
        }

        $courses = $query->latest('id')->paginate(15)->withQueryString();

        // Dynamic Filter Data
        $countries = Country::where('status', 1)->orderBy('name')->get();
        
        $provinces = University::whereNotNull('state_province')
            ->where('state_province', '!=', '')
            ->distinct()
            ->orderBy('state_province')
            ->pluck('state_province');

        $countryProvincesMap = University::whereNotNull('country_id')
            ->whereNotNull('state_province')
            ->where('state_province', '!=', '')
            ->select('country_id', 'state_province')
            ->distinct()
            ->orderBy('state_province')
            ->get()
            ->groupBy('country_id')
            ->map(fn($rows) => $rows->pluck('state_province')->unique()->values()->toArray())
            ->toArray();

        $existingStudyAreas = Course::where('status', 1)
            ->whereNotNull('study_area')
            ->where('study_area', '!=', '')
            ->distinct()
            ->orderBy('study_area')
            ->pluck('study_area');

        $studyAreas = $existingStudyAreas->isNotEmpty()
            ? $existingStudyAreas->values()
            : collect(Course::COMMON_STUDY_AREAS)->sort()->values();

        $studyDisciplineMap = Course::where('status', 1)
            ->whereNotNull('study_area')
            ->where('study_area', '!=', '')
            ->whereNotNull('discipline_area')
            ->where('discipline_area', '!=', '')
            ->select('study_area', 'discipline_area')
            ->distinct()
            ->orderBy('study_area')
            ->orderBy('discipline_area')
            ->get()
            ->groupBy('study_area')
            ->map(fn($rows) => $rows->pluck('discipline_area')->unique()->values()->toArray())
            ->toArray();

        $disciplineAreas = Course::where('status', 1)
            ->whereNotNull('discipline_area')
            ->where('discipline_area', '!=', '')
            ->distinct()
            ->orderBy('discipline_area')
            ->pluck('discipline_area');

        $programLevels = Course::getProgramLevels();
        $requirementsList = Course::getRequirementsList();

        $intakeMonths = ['All', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $intakeYears = ['2025', '2026', '2027', '2028', '2029'];
        $durations = [
            'less_than_1_year' => 'Less than 1 Year',
            '1_2_years' => '1 - 2 Years',
            '3_4_years' => '3 - 4 Years',
            '4_plus_years' => '4+ Years',
        ];

        // Costing Templates & Presets for Search Program Integration
        $allCostingTemplates = CostingTemplate::with('items')->where('is_active', true)->get();
        $costingTemplatesMap = [];
        $defaultCostingTemplate = null;

        foreach ($allCostingTemplates as $tpl) {
            if ($tpl->country_id) {
                $costingTemplatesMap[$tpl->country_id] = $tpl;
            } else {
                $defaultCostingTemplate = $tpl;
            }
        }

        // Active students for quick "Assign to Student" dropdown
        $studentsList = Student::orderBy('first_name')
            ->select('id', 'first_name', 'last_name', 'passport_number', 'country_id', 'package_amount')
            ->get();

        return view('admin.search-program.index', compact(
            'courses',
            'countries',
            'provinces',
            'countryProvincesMap',
            'studyAreas',
            'studyDisciplineMap',
            'disciplineAreas',
            'programLevels',
            'requirementsList',
            'intakeMonths',
            'intakeYears',
            'durations',
            'costingTemplatesMap',
            'defaultCostingTemplate',
            'studentsList'
        ));
    }

    /**
     * Save customized costing breakdown snapshot for an active student.
     */
    public function saveStudentCosting(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'nullable|exists:courses,id',
            'university_id' => 'nullable|exists:universities,id',
            'country_id' => 'nullable|exists:countries,id',
            'costing_template_id' => 'nullable|exists:costing_templates,id',
            'currency' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.01',
            'tuition_fee' => 'required|numeric',
            'scholarship_amount' => 'required|numeric',
            'net_tuition_fee' => 'required|numeric',
            'application_fee' => 'required|numeric',
            'total_additional_costs' => 'required|numeric',
            'agency_fee' => 'required|numeric',
            'grand_total_bdt' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string',
            'items.*.title' => 'required|string',
            'items.*.payable_to' => 'required|string',
            'items.*.currency' => 'required|string',
            'items.*.foreign_amount' => 'required|numeric',
            'items.*.exchange_rate' => 'required|numeric',
            'items.*.bdt_amount' => 'required|numeric',
            'items.*.is_deduction' => 'nullable|boolean',
            'sync_package_amount' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $costing = StudentCosting::create([
            'student_id' => $student->id,
            'course_id' => $validated['course_id'] ?? null,
            'university_id' => $validated['university_id'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'costing_template_id' => $validated['costing_template_id'] ?? null,
            'created_by' => auth()->id(),
            'reference_no' => 'Q-' . strtoupper(uniqid()),
            'currency' => $validated['currency'],
            'exchange_rate' => $validated['exchange_rate'],
            'tuition_fee' => $validated['tuition_fee'],
            'scholarship_amount' => $validated['scholarship_amount'],
            'net_tuition_fee' => $validated['net_tuition_fee'],
            'application_fee' => $validated['application_fee'],
            'total_additional_costs' => $validated['total_additional_costs'],
            'agency_fee' => $validated['agency_fee'],
            'grand_total_bdt' => $validated['grand_total_bdt'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $index => $item) {
            $costing->items()->create([
                'category' => $item['category'],
                'title' => $item['title'],
                'payable_to' => $item['payable_to'],
                'currency' => $item['currency'],
                'foreign_amount' => $item['foreign_amount'],
                'exchange_rate' => $item['exchange_rate'],
                'bdt_amount' => $item['bdt_amount'],
                'is_deduction' => !empty($item['is_deduction']),
                'sort_order' => $index + 1,
            ]);
        }

        // Sync agency fee into student package amount if opted
        if (!empty($validated['sync_package_amount']) && $validated['agency_fee'] > 0) {
            $student->update(['package_amount' => $validated['agency_fee']]);
        }

        $courseName = $costing->course?->name ?? 'Custom Program';
        $student->logActivity(
            'note',
            'Costing Breakdown Assigned: ' . $courseName,
            'Estimated Grand Total: BDT ' . number_format($costing->grand_total_bdt, 2) . ' (Tuition: BDT ' . number_format($costing->net_tuition_fee, 2) . ', Agency: BDT ' . number_format($costing->agency_fee, 2) . ')',
            ['student_costing_id' => $costing->id, 'created_by' => auth()->user()?->name]
        );

        return response()->json([
            'success' => true,
            'message' => 'Costing breakdown successfully assigned to ' . $student->full_name . '.',
            'costing_id' => $costing->id,
            'student_name' => $student->full_name,
        ]);
    }

    /**
     * Printable Insaf Pad quotation sheet.
     */
    public function printQuotation(Request $request)
    {
        $payload = $request->input('quotation_data');
        if (is_string($payload)) {
            $data = json_decode($payload, true) ?: [];
        } else {
            $data = $request->all();
        }

        $student = null;
        if (!empty($data['student_id'])) {
            $student = Student::find($data['student_id']);
        }

        return view('admin.search-program.quotation-pad', compact('data', 'student'));
    }
}

