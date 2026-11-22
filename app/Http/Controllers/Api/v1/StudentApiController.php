<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\StudentDetailResource;
use App\Http\Resources\v1\StudentResource;
use App\Models\Country;
use App\Models\CountryRemark;
use App\Models\CourseIntake;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentApiController extends BaseApiController
{
    /**
     * Display a paginated listing of students with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('*consultant');

        $query = Student::query()
            ->with(['country', 'university', 'course', 'intake', 'marketingAssignee', 'consultantAssignee', 'applicationAssignee', 'creator'])
            ->withSum('payments', 'amount');

        // Text search across personal, contact, passport, and parental fields
        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('passport_number', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('mother_name', 'like', "%{$search}%")
                    ->orWhere('sponsor_phone', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Country filter
        if ($countryId = $request->get('country_id')) {
            $query->where('country_id', $countryId);
        }

        // University filter
        if ($universityId = $request->get('university_id')) {
            $query->where('university_id', $universityId);
        }

        // Remarks filter
        if ($remarks = $request->get('remarks')) {
            $query->where('remarks', $remarks);
        }

        // Collected / Created By filter
        if ($collectedBy = $request->get('collected_by', $request->get('created_by'))) {
            $query->where('created_by', $collectedBy);
        }

        // Payment status filter
        if ($paymentStatus = $request->get('payment_status')) {
            if ($paymentStatus === 'paid') {
                $query->whereNotNull('package_amount')
                    ->where('package_amount', '>', 0)
                    ->havingRaw('COALESCE(payments_sum_amount, 0) >= package_amount');
            } elseif ($paymentStatus === 'partial') {
                $query->whereNotNull('package_amount')
                    ->where('package_amount', '>', 0)
                    ->havingRaw('COALESCE(payments_sum_amount, 0) > 0 AND COALESCE(payments_sum_amount, 0) < package_amount');
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereNotNull('package_amount')
                    ->where('package_amount', '>', 0)
                    ->havingRaw('COALESCE(payments_sum_amount, 0) = 0');
            } elseif ($paymentStatus === 'no_package') {
                $query->where(function ($q) {
                    $q->whereNull('package_amount')->orWhere('package_amount', 0);
                });
            }
        }

        $perPage = (int) $request->get('per_page', 20);
        $students = $query->latest('id')->paginate($perPage);

        return $this->sendPaginated(
            StudentResource::collection($students),
            'Students retrieved successfully.'
        );
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('*consultant');

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

        $student = Student::create($validated);
        $student->load(['country', 'university', 'course', 'intake', 'marketingAssignee', 'consultantAssignee', 'applicationAssignee', 'creator']);

        return $this->sendResponse(
            new StudentDetailResource($student),
            'Student registered successfully.',
            201
        );
    }

    /**
     * Display the specified student dossier.
     */
    public function show(Student $student): JsonResponse
    {
        $this->authorize('*consultant');

        $student->load([
            'country',
            'university',
            'course',
            'intake',
            'marketingAssignee',
            'consultantAssignee',
            'applicationAssignee',
            'creator',
            'applications.university',
            'applications.course',
            'payments',
            'activities.user',
        ]);

        return $this->sendResponse(
            new StudentDetailResource($student),
            'Student dossier retrieved successfully.'
        );
    }

    /**
     * Update the specified student profile.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $this->authorize('*consultant');

        $this->normalizeCourseIntakeMonth($request);

        $validated = $this->validateStudent($request);

        $documents = $student->documents ?? [];
        if ($request->has('delete_documents')) {
            foreach ($request->delete_documents as $index) {
                if (isset($documents[$index])) {
                    Storage::disk('public')->delete($documents[$index]['path']);
                    unset($documents[$index]);
                }
            }
            $documents = array_values($documents);
        }

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
        if ($request->has('delete_translation_documents')) {
            foreach ($request->delete_translation_documents as $index) {
                if (isset($tDocuments[$index])) {
                    Storage::disk('public')->delete($tDocuments[$index]['path']);
                    unset($tDocuments[$index]);
                }
            }
            $tDocuments = array_values($tDocuments);
        }

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
            unset($validated['password'], $validated['plain_password']);
        }

        $student->update($validated);
        $student->load([
            'country',
            'university',
            'course',
            'intake',
            'marketingAssignee',
            'consultantAssignee',
            'applicationAssignee',
            'creator',
            'applications.university',
            'applications.course',
            'payments',
        ]);

        return $this->sendResponse(
            new StudentDetailResource($student),
            'Student profile updated successfully.'
        );
    }

    /**
     * Quick update remarks and notes.
     */
    public function quickUpdate(Request $request, Student $student): JsonResponse
    {
        $this->authorize('*consultant');

        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $student->update([
            'remarks' => $validated['remarks'] ?? $student->remarks,
            'notes' => $validated['notes'] ?? $student->notes,
        ]);

        return $this->sendResponse([
            'id' => $student->id,
            'remarks' => $student->remarks,
            'notes' => $student->notes,
        ], 'Student status and remarks updated successfully.');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): JsonResponse
    {
        $this->authorize('*consultant');

        try {
            $student->delete();
            return $this->sendResponse(null, 'Student record deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint')) {
                return $this->sendError('Cannot delete this student because they have linked applications or payments.', [], 422);
            }
            throw $e;
        }
    }

    /**
     * Get dynamic country remarks options.
     */
    public function countryRemarks(Request $request): JsonResponse
    {
        $countryName = $request->query('country_name');
        $countryId = $request->query('country_id');

        if (!$countryName && $countryId) {
            $country = Country::find($countryId);
            $countryName = $country?->name;
        }

        $remarks = CountryRemark::getRemarksForCountry($countryId ? (int) $countryId : null, $countryName);

        return $this->sendResponse([
            'country' => $countryName,
            'remarks' => $remarks,
        ], 'Country remarks retrieved successfully.');
    }

    /**
     * Validation rules for student.
     */
    private function validateStudent(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'passport_validity' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'dob' => ['nullable', 'date'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'assigned_marketing_id' => ['nullable', 'exists:users,id'],
            'assigned_consultant_id' => ['nullable', 'exists:users,id'],
            'assigned_application_id' => ['nullable', 'exists:users,id'],
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
            'remarks' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'translation_documents' => ['nullable', 'array'],
            'translation_documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);
    }

    private function normalizeCourseIntakeMonth(Request $request): void
    {
        $month = $request->input('course_intake_id');

        $intakeMonths = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];

        if (!$request->filled('course_id') || !in_array($month, $intakeMonths, true)) {
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

    private function sanitizeFilename(string $filename): string
    {
        $filename = strip_tags($filename);
        $filename = preg_replace('/[<>:"|?*\\x00-\\x1f]/', '', $filename);
        $filename = preg_replace('/[\'"&<>()\[\]{};`]/', '_', $filename);
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($basename, 0, 250 - strlen($extension)) . '.' . $extension;
        }
        return $filename;
    }
}
