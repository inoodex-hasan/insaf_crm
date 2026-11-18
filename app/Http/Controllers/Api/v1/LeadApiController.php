<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\LeadResource;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadSubmitted;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class LeadApiController extends BaseApiController
{
    /**
     * Display a listing of marketing leads.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('*marketing');

        $canViewAllLeads = $this->canViewAllLeads();
        $query = Lead::with(['creator', 'country', 'course', 'consultant']);

        if (!$canViewAllLeads) {
            $query->where('created_by', Auth::id());
        }

        // Search by student name or phone
        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($source = $request->get('source')) {
            $query->where('source', $source);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        if ($canViewAllLeads && ($collectedBy = $request->get('collected_by'))) {
            $query->where('created_by', $collectedBy);
        }

        $dateType = in_array($request->input('date_type'), ['created_at', 'next_follow_up_at'])
            ? $request->input('date_type')
            : 'created_at';

        if ($fromDate = $request->get('from_date')) {
            $query->whereDate($dateType, '>=', $fromDate);
        }
        if ($toDate = $request->get('to_date')) {
            $query->whereDate($dateType, '<=', $toDate);
        }

        $perPage = (int) $request->get('per_page', 15);
        $leads = $query->latest('id')->paginate($perPage);

        return $this->sendPaginated(
            LeadResource::collection($leads),
            'Leads retrieved successfully.'
        );
    }

    /**
     * Store a newly created lead.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('*marketing');

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'current_education' => 'nullable|string|max:255',
            'preferred_country' => 'nullable|exists:countries,id',
            'preferred_course' => 'nullable|exists:courses,id',
            'source' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,medium,high',
            'next_follow_up_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $payload = $this->prepareLeadPayload($validated);
        $lead = Lead::create($payload);

        // Notify Operational Team
        $consultants = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['consultant', 'marketing', 'application'])
              ->orWhereIn('name', ['Consultant', 'Marketing', 'Application']);
        })->get();

        if ($consultants->isNotEmpty()) {
            Notification::send($consultants, new NewLeadSubmitted($lead));
        }

        $lead->load(['creator', 'country', 'course', 'consultant']);

        return $this->sendResponse(
            new LeadResource($lead),
            'Lead captured successfully.',
            201
        );
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead): JsonResponse
    {
        $this->authorize('*marketing');
        $this->authorizeLeadOwner($lead);

        $lead->load(['creator', 'country', 'course', 'consultant']);

        return $this->sendResponse(
            new LeadResource($lead),
            'Lead details retrieved successfully.'
        );
    }

    /**
     * Update the specified lead.
     */
    public function update(Request $request, Lead $lead): JsonResponse
    {
        $this->authorize('*marketing');
        $this->authorizeLeadOwner($lead);

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'current_education' => 'nullable|string|max:255',
            'preferred_country' => 'nullable|exists:countries,id',
            'preferred_course' => 'nullable|exists:courses,id',
            'source' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'next_follow_up_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $payload = $this->prepareLeadPayload($validated, $lead);
        $lead->update($payload);
        $lead->load(['creator', 'country', 'course', 'consultant']);

        return $this->sendResponse(
            new LeadResource($lead),
            'Lead updated successfully.'
        );
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(Lead $lead): JsonResponse
    {
        $this->authorize('*marketing');
        $this->authorizeLeadOwner($lead);

        try {
            $lead->delete();
            return $this->sendResponse(null, 'Lead deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint')) {
                return $this->sendError('Cannot delete this lead because it has linked records.', [], 422);
            }
            throw $e;
        }
    }

    private function authorizeLeadOwner(Lead $lead): void
    {
        if (!$this->canViewAllLeads() && (int) $lead->created_by !== Auth::id()) {
            abort(403, 'You can only access leads that you created.');
        }
    }

    private function canViewAllLeads(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Triad roles or Admin can view all leads
        return $user->hasRole('admin')
            || $user->hasRole('super-admin')
            || $user->hasRole('superadmin')
            || $user->hasRole('consultant')
            || $user->hasRole('marketing')
            || $user->hasRole('application');
    }

    private function prepareLeadPayload(array $validated, ?Lead $lead = null): array
    {
        if ($lead === null) {
            $validated['created_by'] = Auth::id();
            $validated['status'] = $validated['status'] ?? 'pending';
            $validated['priority'] = $validated['priority'] ?? 'low';
        } else {
            $validated['status'] = $validated['status'] ?? $lead->status;
            $validated['priority'] = $validated['priority'] ?? $lead->priority ?? 'low';
        }

        if (Schema::hasColumn('leads', 'follow_up_history')) {
            $validated['follow_up_history'] = $this->buildFollowUpHistory(
                $lead,
                $validated['next_follow_up_at'] ?? null,
                $validated['notes'] ?? null
            );
        }

        return $validated;
    }

    private function buildFollowUpHistory(?Lead $lead, mixed $nextFollowUpAt, ?string $notes): ?array
    {
        $history = collect($lead?->follow_up_history ?? [])
            ->map(fn ($entry) => $this->normalizeFollowUpHistoryEntry($entry))
            ->filter(fn ($entry) => $entry !== null)
            ->values();

        $existingEntry = $this->normalizeFollowUpHistoryEntry([
            'date' => $lead?->next_follow_up_at,
            'notes' => $lead?->notes,
        ]);

        if ($existingEntry !== null && !$this->followUpEntriesMatch($history->last(), $existingEntry)) {
            $history->push($existingEntry);
        }

        if (filled($nextFollowUpAt)) {
            $nextEntry = $this->normalizeFollowUpHistoryEntry([
                'date' => $nextFollowUpAt,
                'notes' => $notes,
            ]);

            if ($nextEntry !== null && !$this->followUpEntriesMatch($history->last(), $nextEntry)) {
                $history->push($nextEntry);
            }
        }

        $history = $history->filter(fn ($entry) => $entry !== null)->values()->all();

        return empty($history) ? null : $history;
    }

    private function normalizeFollowUpHistoryEntry(mixed $entry): ?array
    {
        if (blank($entry)) {
            return null;
        }

        if (!is_array($entry)) {
            return [
                'date' => Carbon::parse($entry)->toDateString(),
                'notes' => null,
            ];
        }

        $date = $entry['date'] ?? $entry['follow_up_date'] ?? $entry['next_follow_up_at'] ?? null;

        if (blank($date)) {
            return null;
        }

        return [
            'date' => Carbon::parse($date)->toDateString(),
            'notes' => $entry['notes'] ?? null,
        ];
    }

    private function followUpEntriesMatch(?array $left, ?array $right): bool
    {
        if ($left === null || $right === null) {
            return false;
        }

        return $left['date'] === $right['date']
            && (string) ($left['notes'] ?? '') === (string) ($right['notes'] ?? '');
    }
}
