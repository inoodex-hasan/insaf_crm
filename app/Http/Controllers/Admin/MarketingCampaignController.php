<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MarketingCampaignController extends Controller
{

    public function index(Request $request)
    {
        $query = MarketingCampaign::with('creator')->latest();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status = $request->get('boosting_status')) {
            $query->where('boosting_status', $status);
        }

        $campaigns = $query->paginate(15)->withQueryString();

        return view('admin.marketing.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return redirect()->route('admin.marketing.campaigns.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'boosting_status' => ['required', Rule::in(['on', 'off'])],
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        MarketingCampaign::create($validated);

        return redirect()
            ->route('admin.marketing.campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function show(MarketingCampaign $campaign)
    {
        $campaign->load('creator');
        return view('admin.marketing.campaigns.show', compact('campaign'));
    }

    public function edit(MarketingCampaign $campaign)
    {
        return redirect()->route('admin.marketing.campaigns.index');
    }

    public function update(Request $request, MarketingCampaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'boosting_status' => ['required', Rule::in(['on', 'off'])],
            'notes' => 'nullable|string',
        ]);

        $campaign->update($validated);

        return redirect()
            ->route('admin.marketing.campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(MarketingCampaign $campaign)
    {
        return $this->safeDelete($campaign, 'admin.marketing.campaigns.index', [], 'Campaign deleted successfully.');
    }

    public function toggleBoosting(MarketingCampaign $campaign)
    {
        $campaign->boosting_status = $campaign->boosting_status === 'on' ? 'off' : 'on';
        $campaign->save();

        return response()->json([
            'status' => 'success',
            'boosting_status' => $campaign->boosting_status
        ]);
    }

}
