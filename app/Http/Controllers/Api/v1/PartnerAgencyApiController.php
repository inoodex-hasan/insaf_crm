<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\PartnerAgencyResource;
use App\Models\PartnerAgency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerAgencyApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $query = PartnerAgency::withCount('commissions');

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('specialized_countries', 'like', "%{$search}%");
            });
        }

        $agencies = $query->orderBy('name')->paginate($request->input('per_page', 20));

        return $this->sendPaginated(
            $agencies->through(fn($a) => new PartnerAgencyResource($a)),
            'Partner agencies retrieved successfully.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'specialized_countries' => 'nullable|string|max:255',
            'commission_terms' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);

        $validated['status'] = $request->input('status', 1);
        $validated['created_by'] = Auth::id();

        $agency = PartnerAgency::create($validated);

        return $this->sendResponse(
            new PartnerAgencyResource($agency),
            'Partner agency created successfully.',
            201
        );
    }

    public function show(PartnerAgency $partnerAgency): JsonResponse
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $partnerAgency->loadCount('commissions');

        return $this->sendResponse(
            new PartnerAgencyResource($partnerAgency),
            'Partner agency retrieved successfully.'
        );
    }

    public function update(Request $request, PartnerAgency $partnerAgency): JsonResponse
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'specialized_countries' => 'nullable|string|max:255',
            'commission_terms' => 'nullable|string',
            'status' => 'required|integer',
        ]);

        $partnerAgency->update($validated);

        return $this->sendResponse(
            new PartnerAgencyResource($partnerAgency),
            'Partner agency updated successfully.'
        );
    }

    public function destroy(PartnerAgency $partnerAgency): JsonResponse
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        if ($partnerAgency->commissions()->count() > 0) {
            return $this->sendError('Cannot delete agency with linked commissions.', [], 422);
        }

        $partnerAgency->delete();

        return $this->sendResponse(null, 'Partner agency deleted successfully.');
    }
}
