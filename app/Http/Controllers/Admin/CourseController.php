<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Course, University};

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->with('university.country');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($universityId = $request->get('university_id')) {
            $query->where('university_id', $universityId);
        }

        if ($studyArea = $request->get('study_area')) {
            $query->where('study_area', $studyArea);
        }

        if ($programLevel = $request->get('program_level')) {
            $query->where('program_level', $programLevel);
        }

        $courses = $query->latest()->paginate(15)->withQueryString();
        $universities = University::where('status', 1)->orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'universities'));
    }

    public function create()
    {
        $universities = University::where('status', 1)->orderBy('name')->get();
        $programLevels = Course::getProgramLevels();
        $requirementsList = Course::getRequirementsList();
        
        $existingStudyAreas = Course::whereNotNull('study_area')->distinct()->pluck('study_area');
        $studyAreas = collect(Course::COMMON_STUDY_AREAS)->merge($existingStudyAreas)->filter()->unique()->sort()->values();

        return view('admin.courses.create', compact('universities', 'programLevels', 'requirementsList', 'studyAreas'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCourse($request);
        $validated['esl_available'] = $request->boolean('esl_available');
        $validated['stem_program'] = $request->boolean('stem_program');

        Course::create($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load('university.country', 'intakes');
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $universities = University::where('status', 1)->orderBy('name')->get();
        $programLevels = Course::getProgramLevels();
        $requirementsList = Course::getRequirementsList();

        $existingStudyAreas = Course::whereNotNull('study_area')->distinct()->pluck('study_area');
        $studyAreas = collect(Course::COMMON_STUDY_AREAS)->merge($existingStudyAreas)->filter()->unique()->sort()->values();

        return view('admin.courses.edit', compact('course', 'universities', 'programLevels', 'requirementsList', 'studyAreas'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $this->validateCourse($request);
        $validated['esl_available'] = $request->boolean('esl_available');
        $validated['stem_program'] = $request->boolean('stem_program');
        $validated['requirements'] = $request->input('requirements', []);
        $validated['available_intake_months'] = $request->input('available_intake_months', []);
        $validated['intake_years'] = $request->input('intake_years', []);

        $course->update($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        return $this->safeDelete($course, 'admin.courses.index', [], 'Course deleted successfully.');
    }

    private function validateCourse(Request $request): array
    {
        return $request->validate([
            'university_id' => ['required', 'exists:universities,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'degree_level' => ['nullable', 'string', 'max:100'],
            'program_level' => ['nullable', 'string', 'max:50'],
            'study_area' => ['nullable', 'string', 'max:255'],
            'discipline_area' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:100'],
            'duration_type' => ['nullable', 'string', 'max:50'],
            'tuition_fee' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'max:10'],
            'requirements' => ['nullable', 'array'],
            'available_intake_months' => ['nullable', 'array'],
            'intake_years' => ['nullable', 'array'],
            'esl_available' => ['nullable', 'boolean'],
            'stem_program' => ['nullable', 'boolean'],
            'status' => ['required', 'boolean'],
        ]);
    }
}
