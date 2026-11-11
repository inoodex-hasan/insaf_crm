<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{

    public function index(Request $request)
    {
        $query = Country::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        $countries = $query->latest()->paginate(15)->withQueryString();

        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return redirect()->route('admin.countries.index');
    }

    public function store(Request $request)
    {

        $validated = $this->validateCountry($request);

        Country::create($validated);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Country created successfully.');
    }

    public function edit(Country $country)
    {
        return redirect()->route('admin.countries.index');
    }

    public function update(Request $request, Country $country)
    {

        $validated = $this->validateCountry($request);

        $country->update($validated);

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        return $this->safeDelete($country, 'admin.countries.index', [], 'Country deleted successfully.');
    }

    private function validateCountry(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'boolean'],
        ]);
    }
}
