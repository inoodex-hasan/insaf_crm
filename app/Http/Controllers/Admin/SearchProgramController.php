<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Country;
use App\Models\University;
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

        $existingStudyAreas = Course::whereNotNull('study_area')
            ->where('study_area', '!=', '')
            ->distinct()
            ->pluck('study_area');
        $studyAreas = collect(Course::COMMON_STUDY_AREAS)
            ->merge($existingStudyAreas)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $disciplineAreas = Course::whereNotNull('discipline_area')
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

        return view('admin.search-program.index', compact(
            'courses',
            'countries',
            'provinces',
            'studyAreas',
            'disciplineAreas',
            'programLevels',
            'requirementsList',
            'intakeMonths',
            'intakeYears',
            'durations'
        ));
    }
}

