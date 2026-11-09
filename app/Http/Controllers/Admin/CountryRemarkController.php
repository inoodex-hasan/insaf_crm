<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountryRemark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CountryRemarkController extends Controller
{
    /**
     * Display a listing of remarks grouped by country or filtered.
     */
    public function index(Request $request): View
    {
        $selectedCountryId = $request->query('country_id'); // 'all' or specific ID

        // Strictly keep only 3 countries: Malta, Russia, Malaysia
        $countries = Country::whereIn('name', ['Malta', 'Russia', 'Malaysia'])
            ->orderByRaw("FIELD(name, 'Malta', 'Russia', 'Malaysia')")
            ->get(['id', 'name']);

        $countryIds = $countries->pluck('id')->toArray();

        $query = CountryRemark::with('country')
            ->whereIn('country_id', $countryIds)
            ->ordered();

        if (!empty($selectedCountryId) && $selectedCountryId !== 'all') {
            $query->where('country_id', (int) $selectedCountryId);
        }

        $remarks = $query->get();

        // Calculate counts per country
        $counts = [
            'total' => CountryRemark::whereIn('country_id', $countryIds)->count(),
        ];
        foreach ($countries as $country) {
            $counts[$country->id] = CountryRemark::where('country_id', $country->id)->count();
        }

        $colorOptions = [
            'primary' => 'Blue (Primary)',
            'success' => 'Green (Success)',
            'danger' => 'Red (Danger / Refused)',
            'warning' => 'Amber (Pending / Waiting)',
            'info' => 'Teal / Cyan (Progress / In-flight)',
            'secondary' => 'Gray (Neutral)',
            'dark' => 'Dark',
        ];

        return view('admin.country-remarks.index', compact(
            'remarks',
            'countries',
            'counts',
            'selectedCountryId',
            'colorOptions'
        ));
    }

    /**
     * Store a newly created remark.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'in:primary,secondary,success,danger,warning,info,dark'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        CountryRemark::create([
            'country_id' => (int) $validated['country_id'],
            'name' => trim($validated['name']),
            'color' => $validated['color'] ?? 'primary',
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        return redirect()->back()->with('success', 'Country remark created successfully.');
    }

    /**
     * Update an existing remark.
     */
    public function update(Request $request, CountryRemark $remark): RedirectResponse
    {
        $validated = $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'in:primary,secondary,success,danger,warning,info,dark'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $remark->update([
            'country_id' => (int) $validated['country_id'],
            'name' => trim($validated['name']),
            'color' => $validated['color'] ?? 'primary',
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : false,
        ]);

        return redirect()->back()->with('success', 'Country remark updated successfully.');
    }

    /**
     * Delete a remark.
     */
    public function destroy(CountryRemark $remark): RedirectResponse
    {
        $remark->delete();

        return redirect()->back()->with('success', 'Remark deleted successfully.');
    }

    /**
     * Toggle active status via AJAX or standard POST.
     */
    public function toggleStatus(CountryRemark $remark): JsonResponse|RedirectResponse
    {
        $remark->is_active = !$remark->is_active;
        $remark->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $remark->is_active,
                'message' => 'Status updated.',
            ]);
        }

        return redirect()->back()->with('success', 'Remark status toggled.');
    }
}
