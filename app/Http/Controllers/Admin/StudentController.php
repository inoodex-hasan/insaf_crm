<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\{Country, Course, CourseIntake, Lead, Student, University, User};
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{

    public function index(Request $request)
    {
        $query = Student::query()->with(['marketingAssignee', 'consultantAssignee', 'applicationAssignee', 'creator']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($stage = $request->get('stage')) {
            $query->where('current_stage', $stage);
        }

        if ($status = $request->get('status')) {
            $query->where('current_status', $status);
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create(Request $request)
    {
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'marketing');
        })->orderBy('name')->get(['id', 'name']);
        $countries = Country::orderBy('name')->get(['id', 'name']);
        $universities = University::where('status', '1')->orderBy('name')->get(['id', 'name', 'country_id']);

        // Auto-assign marketing from lead's created_by if lead_id is provided
        $assignedMarketingId = null;
        if ($request->has('lead_id')) {
            $lead = Lead::find($request->lead_id);
            if ($lead && $lead->created_by) {
                $assignedMarketingId = $lead->created_by;
            }
        }

        return view('admin.students.create', compact('users', 'countries', 'universities', 'assignedMarketingId'));
    }

    public function store(Request $request)
    {
        $this->normalizeCourseIntakeMonth($request);

        $validated = $this->validateStudent($request);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('documents/students', 'public');
                $documents[] = [
                    'name' => $this->sanitizeFilename($file->getClientOriginalName()),
                    'path' => $path,
                ];
            }
            $validated['documents'] = $documents;
        }

        if ($request->hasFile('translation_documents')) {
            $tDocuments = [];
            foreach ($request->file('translation_documents') as $file) {
                $path = $file->store('documents/students/translations', 'public');
                $tDocuments[] = [
                    'name' => $this->sanitizeFilename($file->getClientOriginalName()),
                    'path' => $path,
                ];
            }
            $validated['translation_documents'] = $tDocuments;
        }

        if (!empty($validated['password'])) {
            $validated['plain_password'] = $validated['password'];
            $validated['password'] = Hash::make($validated['password']);
        }

        Student::create($validated);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {

        $student->load(['marketingAssignee', 'consultantAssignee', 'applicationAssignee', 'creator', 'country', 'university', 'course', 'intake', 'applications.university', 'applications.course', 'applications.intake']);

        return view('admin.students.show', compact('student'));
    }

    public function downloadPdf(Student $student)
    {
        $this->authorize('*consultant');

        $student->load(['country', 'university', 'course', 'intake', 'marketingAssignee', 'consultantAssignee']);

        try {
            $pdf = Pdf::loadView('admin.students.pdf.student-detail', compact('student'));
            $pdf->setPaper('a4', 'portrait');
            
            return $pdf->stream('student-' . $student->id . '-' . $student->first_name . '.pdf');
        } catch (\Exception $e) {
            \Log::error('PDF generation failed for student ' . $student->id . ': ' . $e->getMessage());
            return redirect()->route('admin.students.show', $student->id)
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function edit(Student $student)
    {
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'marketing');
        })->orderBy('name')->get(['id', 'name']);
        $countries = Country::orderBy('name')->get(['id', 'name']);
        $universities = University::where('status', '1')->orderBy('name')->get(['id', 'name', 'country_id']);
        $courses = $student->university_id ? Course::where('university_id', $student->university_id)->get(['id', 'name']) : [];
        $intakes = $student->course_id ? CourseIntake::where('course_id', $student->course_id)->get(['id', 'intake_name']) : [];

        return view('admin.students.edit', compact('student', 'users', 'countries', 'universities', 'courses', 'intakes'));
    }

    public function update(Request $request, Student $student)
    {
        $this->normalizeCourseIntakeMonth($request);

        $validated = $this->validateStudent($request);

        $documents = $student->documents ?? [];

        // Handle deletions
        if ($request->has('delete_documents')) {
            foreach ($request->delete_documents as $index) {
                if (isset($documents[$index])) {
                    Storage::disk('public')->delete($documents[$index]['path']);
                    unset($documents[$index]);
                }
            }
            $documents = array_values($documents); // Re-index array
        }

        // Handle new uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('documents/students', 'public');
                $documents[] = [
                    'name' => $this->sanitizeFilename($file->getClientOriginalName()),
                    'path' => $path,
                ];
            }
        }

        $validated['documents'] = $documents;

        $tDocuments = $student->translation_documents ?? [];
        // Handle translation deletions
        if ($request->has('delete_translation_documents')) {
            foreach ($request->delete_translation_documents as $index) {
                if (isset($tDocuments[$index])) {
                    Storage::disk('public')->delete($tDocuments[$index]['path']);
                    unset($tDocuments[$index]);
                }
            }
            $tDocuments = array_values($tDocuments);
        }
        // Handle new translation uploads
        if ($request->hasFile('translation_documents')) {
            foreach ($request->file('translation_documents') as $file) {
                $path = $file->store('documents/students/translations', 'public');
                $tDocuments[] = [
                    'name' => $this->sanitizeFilename($file->getClientOriginalName()),
                    'path' => $path,
                ];
            }
        }
        $validated['translation_documents'] = $tDocuments;

        if (!empty($validated['password'])) {
            $validated['plain_password'] = $validated['password'];
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
            unset($validated['plain_password']);
        }

        $student->update($validated);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        return $this->safeDelete($student, 'admin.students.index', [], 'Student deleted successfully.');
    }

    private function validateStudent(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'dob' => ['nullable', 'date'],
            'country_id' => ['nullable', 'exists:countries,id'],
            // 'current_stage' => [
            //     'required',
            //     Rule::in(['lead', 'counseling', 'payment', 'application', 'offer', 'visa', 'enrolled']),
            // ],
            // 'current_status' => [
            //     'required',
            //     Rule::in(['pending', 'applied', 'rejected', 'withdrawn', 'visa_processing', 'enrolled']),
            // ],
            'assigned_marketing_id' => ['nullable', 'exists:users,id'],
            // 'assigned_consultant_id' => ['nullable', 'exists:users,id'],
            // 'assigned_application_id' => ['nullable', 'exists:users,id'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'ssc_result' => ['nullable', 'string', 'max:255'],
            'hsc_result' => ['nullable', 'string', 'max:255'],
            'ielts_score' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'package_amount' => ['nullable', 'numeric', 'min:0'],
            'university_id' => ['nullable', 'exists:universities,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'course_intake_id' => ['nullable', 'exists:course_intakes,id'],
            'password' => ['nullable', 'string', 'min:8'],
            'sponsor_phone' => ['nullable', 'string', 'max:50'],
            'passport_validity' => ['nullable', 'date'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'], // 10MB limit
            'translation_documents' => ['nullable', 'array'],
            'translation_documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'], // 10MB limit
        ]);
    }

    private function normalizeCourseIntakeMonth(Request $request): void
    {
        $month = $request->input('course_intake_id');

        if (!$request->filled('course_id') || !in_array($month, $this->intakeMonths(), true)) {
            return;
        }

        $intake = CourseIntake::firstOrCreate(
            [
                'course_id' => $request->input('course_id'),
                'intake_name' => $month,
            ],
            [
                'status' => true,
            ]
        );

        $request->merge(['course_intake_id' => $intake->id]);
    }

    private function intakeMonths(): array
    {
        return [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];
    }

    public function getUniversities($country_id)
    {
        return University::where('country_id', $country_id)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getCourses($university_id)
    {
        return Course::where('university_id', $university_id)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getIntakes($course_id)
    {
        return CourseIntake::where('course_id', $course_id)
            ->where('status', 1)
            ->orderBy('intake_name')
            ->get(['id', 'intake_name']);
    }

    /**
     * Sanitize filename to prevent XSS attacks via malicious filenames
     *
     * @param string $filename
     * @return string
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove any HTML/JS tags
        $filename = strip_tags($filename);

        // Remove special characters that could be used for XSS
        $filename = preg_replace('/[<>:"|?*\\x00-\\x1f]/', '', $filename);

        // Replace potentially dangerous characters with underscores
        $filename = preg_replace('/[\'"&<>()\[\]{};`]/', '_', $filename);

        // Limit length
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($basename, 0, 250 - strlen($extension)) . '.' . $extension;
        }

        return $filename;
    }
}
