<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerAgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerAgencyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        $query = PartnerAgency::withCount('commissions');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $agencies = $query->orderBy('name')->paginate(20)->withQueryString();
        $totalCount = PartnerAgency::count();
        $activeCount = PartnerAgency::where('status', 1)->count();

        return view('admin.partner-agencies.index', compact('agencies', 'totalCount', 'activeCount'));
    }

    public function store(Request $request)
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'agency' => $agency,
                'message' => 'Partner agency added successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Partner agency added successfully.');
    }

    public function update(Request $request, PartnerAgency $partnerAgency)
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'agency' => $partnerAgency,
                'message' => 'Partner agency updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Partner agency updated successfully.');
    }

    public function destroy(PartnerAgency $partnerAgency)
    {
        $this->authorize('*accountant|*consultant|*application|*marketing');

        if ($partnerAgency->commissions()->count() > 0) {
            return redirect()->back()->withErrors(['msg' => 'Cannot delete agency with linked commissions.']);
        }

        $partnerAgency->delete();

        return redirect()->back()->with('success', 'Partner agency deleted successfully.');
    }
}
