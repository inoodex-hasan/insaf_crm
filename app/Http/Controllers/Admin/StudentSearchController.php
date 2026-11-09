<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountryRemark;
use App\Models\Student;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentSearchController extends Controller
{
    /**
     * Display a listing of students with comprehensive search filters.
     */
    public function index(Request $request)
    {
        $query = Student::query()
            ->with(['country', 'university', 'course', 'payments'])
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

        $students = $query->latest('id')->paginate(20)->withQueryString();

        $countries = Country::orderBy('name')->get(['id', 'name']);
        $universities = University::where('status', '1')->orderBy('name')->get(['id', 'name', 'country_id']);
        $allRemarksConfig = CountryRemark::getAllGroupedRemarks();

        // Resolve active country for filter remarks dropdown
        $activeCountryName = null;
        if ($countryId) {
            $activeCountry = $countries->firstWhere('id', (int) $countryId);
            $activeCountryName = $activeCountry?->name;
        }
        $filterRemarks = CountryRemark::getRemarksForCountry($countryId ? (int) $countryId : null, $activeCountryName);

        return view('admin.student-search.index', compact(
            'students',
            'countries',
            'universities',
            'allRemarksConfig',
            'filterRemarks'
        ));
    }

    /**
     * Quick update remarks and notes via AJAX or standard form.
     */
    public function updateRemarksNotes(Request $request, Student $student)
    {
        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $student->update([
            'remarks' => $validated['remarks'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Student remarks and notes updated successfully.',
                'remarks' => $student->remarks,
                'notes' => $student->notes,
            ]);
        }

        return redirect()->back()->with('success', 'Remarks and notes updated successfully.');
    }

    /**
     * Get remarks dropdown list for a country (via AJAX).
     */
    public function getCountryRemarks(Request $request): JsonResponse
    {
        $countryName = $request->query('country_name');
        $countryId = $request->query('country_id');

        if (!$countryName && $countryId) {
            $country = Country::find($countryId);
            $countryName = $country?->name;
        }

        $remarks = CountryRemark::getRemarksForCountry($countryId ? (int) $countryId : null, $countryName);

        return response()->json([
            'country' => $countryName,
            'remarks' => $remarks,
        ]);
    }
}

