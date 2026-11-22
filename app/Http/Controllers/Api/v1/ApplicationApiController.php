<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\ApplicationResource;
use App\Models\Application;
use App\Models\User;
use App\Notifications\NewApplicationNotification;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ApplicationApiController extends BaseApiController
{
    /**
     * Display a paginated listing of student applications with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('*application');

        $query = Application::with(['student', 'university', 'course', 'intake', 'creator']);

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('application_id', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('passport_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->has('offer_letter')) {
            $query->where('offer_letter_received', $request->boolean('offer_letter'));
        }

        if ($visaStatus = $request->get('visa_status')) {
            $query->where('visa_status', $visaStatus);
        }

        if ($emgsScore = $request->get('emgs_score')) {
            $query->where('emgs_score', $emgsScore);
        }

        if ($request->has('security_deposit')) {
            $query->where('security_deposit_status', $request->boolean('security_deposit'));
        }

        if ($request->has('cvu_fee')) {
            $query->where('cvu_fee_status', $request->boolean('cvu_fee'));
        }

        if ($request->has('final_payment')) {
            $query->where('final_payment_status', $request->boolean('final_payment'));
        }

        // Filter by creator / collector
        if ($creatorId = $request->get('created_by', $request->get('collected_by'))) {
            $query->where(function ($q) use ($creatorId) {
                $q->where('created_by', $creatorId)
                  ->orWhereHas('student', function ($sq) use ($creatorId) {
                      $sq->where('created_by', $creatorId);
                  });
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        $applications = $query->latest('id')->paginate($perPage);

        return $this->sendPaginated(
            ApplicationResource::collection($applications),
            'Applications retrieved successfully.'
        );
    }

    /**
     * Store a newly created application.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('*application');

        $validated = $this->validateApplication($request);
        $validated['created_by'] = auth()->id();

        $application = Application::create($validated);

        // Notify Application & Operational Team
        $applicationTeam = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['application', 'consultant', 'marketing'])
              ->orWhereIn('name', ['Application', 'Consultant', 'Marketing']);
        })->get();

        if ($applicationTeam->isNotEmpty()) {
            Notification::send($applicationTeam, new NewApplicationNotification($application));
        }

        $application->load(['student', 'university', 'course', 'intake', 'creator']);

        return $this->sendResponse(
            new ApplicationResource($application),
            'Application lodged successfully.',
            201
        );
    }

    /**
     * Display the specified application details.
     */
    public function show(Application $application): JsonResponse
    {
        $this->authorize('*application');

        $application->load(['student', 'university.country', 'course', 'intake', 'creator', 'payments.collector']);

        return $this->sendResponse(
            new ApplicationResource($application),
            'Application details retrieved successfully.'
        );
    }

    /**
     * Update the specified application.
     */
    public function update(Request $request, Application $application): JsonResponse
    {
        $this->authorize('*application');

        $validated = $this->validateApplication($request, true);

        $application->update($validated);
        $application->load(['student', 'university.country', 'course', 'intake', 'creator', 'payments.collector']);

        return $this->sendResponse(
            new ApplicationResource($application),
            'Application updated successfully.'
        );
    }

    /**
     * Remove the specified application.
     */
    public function destroy(Application $application): JsonResponse
    {
        $this->authorize('*application');

        try {
            $application->delete();
            return $this->sendResponse(null, 'Application deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint')) {
                return $this->sendError('Cannot delete this application because it has associated payments or commissions.', [], 422);
            }
            throw $e;
        }
    }

    /**
     * Retrieve invoice data for the application.
     */
    public function invoiceData(Application $application): JsonResponse
    {
        $this->authorize('*invoice');

        $application->load(['student', 'university', 'course', 'intake', 'payments']);

        return $this->sendResponse([
            'id' => $application->id,
            'application_id' => $application->application_id,
            'student' => [
                'id' => $application->student?->id,
                'name' => $application->student?->full_name,
                'phone' => $application->student?->phone,
                'passport_number' => $application->student?->passport_number,
            ],
            'university' => $application->university?->name ?? '-',
            'course' => $application->course?->name ?? '-',
            'intake' => $application->intake?->intake_name ?? '-',
            'tuition_fee' => (float) ($application->tuition_fee ?? 0),
            'total_fee' => (float) ($application->total_fee ?? 0),
            'tuition_fee_status' => $application->tuition_fee_status,
            'service_charge_status' => $application->service_charge_status,
            'payments' => $application->payments->map(fn($p) => [
                'id' => $p->id,
                'amount' => (float) $p->amount,
                'payment_date' => $p->payment_date?->format('Y-m-d'),
                'payment_method' => $p->payment_method,
            ]),
        ], 'Invoice data retrieved successfully.');
    }

    /**
     * Validation rules for application payload.
     */
    private function validateApplication(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'student_id' => $isUpdate ? 'nullable|exists:students,id' : 'required|exists:students,id',
            'university_id' => 'nullable|exists:universities,id',
            'course_id' => 'nullable|exists:courses,id',
            'course_intake_id' => 'nullable|exists:course_intakes,id',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'application_priority' => 'nullable|in:normal,priority,vip',
            'offer_letter_received' => 'nullable|boolean',
            'vfs_appointment' => 'nullable|boolean',
            'vfs_appointment_date' => 'nullable|date',
            'file_submission' => 'nullable|boolean',
            'file_submission_date' => 'nullable|date',
            'visa_status' => 'nullable|in:not_applied,pending,approved,rejected',
            'visa_decision_date' => 'nullable|date',
            'visa_approval_date' => 'nullable|date',
            'tuition_fee' => 'nullable|numeric|min:0',
            'total_fee' => 'nullable|numeric|min:0',
            'tuition_fee_status' => 'nullable|in:pending,paid,partial',
            'service_charge_status' => 'nullable|in:pending,paid,partial',
            'security_deposit_status' => 'nullable|boolean',
            'cvu_fee_status' => 'nullable|boolean',
            'admission_fee_status' => 'nullable|boolean',
            'final_payment_status' => 'nullable|boolean',
            'emgs_payment_status' => 'nullable|boolean',
            'emgs_score' => 'nullable|integer|min:0|max:100',
            'final_status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'documents_checklist' => 'nullable|array',
        ];

        return $request->validate($rules);
    }
}
