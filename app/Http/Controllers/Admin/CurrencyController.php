<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{

    public function index(Request $request)
    {
        $query = Currency::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        $currencies = $query->latest()->paginate(15)->withQueryString();

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return redirect()->route('admin.currencies.index');
    }

    public function store(Request $request)
    {

        $validated = $this->validateCurrency($request);

        // If setting as default, unset any existing default
        if (!empty($validated['is_default'])) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        Currency::create($validated);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Currency created successfully.');
    }

    public function edit(Currency $currency)
    {
        return redirect()->route('admin.currencies.index');
    }

    public function update(Request $request, Currency $currency)
    {

        $validated = $this->validateCurrency($request, $currency->id);

        // If setting as default, unset any existing default
        if (!empty($validated['is_default'])) {
            Currency::where('is_default', true)
                ->where('id', '!=', $currency->id)
                ->update(['is_default' => false]);
        }

        $currency->update($validated);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        return $this->safeDelete($currency, 'admin.currencies.index', [], 'Currency deleted successfully.');
    }

    private function validateCurrency(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'          => ['required', 'string', 'max:50'],
            'code'          => ['required', 'string', 'max:3', 'unique:currencies,code' . ($ignoreId ? ",{$ignoreId}" : '')],
            'symbol'        => ['required', 'string', 'max:10'],
            'exchange_rate' => ['required', 'numeric', 'min:0'],
            'is_active'     => ['sometimes', 'boolean'],
            'is_default'    => ['sometimes', 'boolean'],
        ]);
    }
}
