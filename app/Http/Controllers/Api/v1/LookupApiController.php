<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\{Country, Course, CourseIntake, Currency, University, User};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupApiController extends BaseApiController
{
    /**
     * Cold-start bundle of common lookup data for mobile caching.
     */
    public function all(): JsonResponse
    {
        $countries = Country::where('status', '1')->orderBy('name')->get(['id', 'name', 'code', 'currency']);
        $currencies = Currency::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'symbol', 'exchange_rate']);
        
        $staff = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['marketing', 'consultant', 'application', 'admin', 'super-admin'])
              ->orWhereIn('name', ['Marketing', 'Consultant', 'Application', 'Admin', 'Super Admin']);
        })->with('roles:id,name,slug')->orderBy('name')->get(['id', 'name', 'email', 'username', 'designation']);

        return $this->sendResponse([
            'countries' => $countries,
            'currencies' => $currencies,
            'staff' => $staff,
            'lead_sources' => [
                'Facebook', 'Instagram', 'Google Search', 'Website', 'Friend / Referral',
                'Walk-in', 'Phone Call', 'WhatsApp', 'Education Fair', 'Agent / Partner', 'Other'
            ],
            'application_statuses' => [
                'pending', 'applied', 'submitted', 'under_review', 'conditional_offer',
                'unconditional_offer', 'offer_received', 'accepted', 'rejected',
                'cancelled', 'withdrawn', 'deferred'
            ],
            'visa_statuses' => [
                'not_applied', 'pending', 'approved', 'rejected'
            ],
        ], 'All lookup metadata retrieved successfully.');
    }

    /**
     * Active Countries.
     */
    public function countries(Request $request): JsonResponse
    {
        $query = Country::where('status', '1');

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $countries = $query->orderBy('name')->get(['id', 'name', 'code', 'currency']);

        return $this->sendResponse($countries, 'Countries retrieved successfully.');
    }

    /**
     * Universities with optional country filter.
     */
    public function universities(Request $request): JsonResponse
    {
        $query = University::where('status', '1');

        if ($countryId = $request->get('country_id')) {
            $query->where('country_id', $countryId);
        }

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%");
            });
        }

        $universities = $query->orderBy('name')->get([
            'id', 'country_id', 'name', 'short_name', 'state_province', 'website', 'email', 'phone'
        ]);

        return $this->sendResponse($universities, 'Universities retrieved successfully.');
    }

    /**
     * Courses with optional university, country, or degree level filters.
     */
    public function courses(Request $request): JsonResponse
    {
        $query = Course::where('status', '1');

        if ($universityId = $request->get('university_id')) {
            $query->where('university_id', $universityId);
        }

        if ($countryId = $request->get('country_id')) {
            $query->whereHas('university', fn($u) => $u->where('country_id', $countryId));
        }

        if ($degreeLevel = $request->get('degree_level')) {
            $query->where('degree_level', $degreeLevel);
        }

        if ($search = trim((string) $request->get('search'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        $courses = $query->orderBy('name')->get([
            'id', 'university_id', 'name', 'degree_level', 'duration', 'duration_type',
            'tuition_fee', 'fee_period', 'currency'
        ]);

        return $this->sendResponse($courses, 'Courses retrieved successfully.');
    }

    /**
     * Course Intakes with optional course filter.
     */
    public function intakes(Request $request): JsonResponse
    {
        $query = CourseIntake::where('status', '1');

        if ($courseId = $request->get('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($search = trim((string) $request->get('search'))) {
            $query->where('intake_name', 'like', "%{$search}%");
        }

        $intakes = $query->orderBy('intake_name')->get([
            'id', 'course_id', 'intake_name', 'application_start_date', 'application_deadline', 'class_start_date'
        ]);

        return $this->sendResponse($intakes, 'Course intakes retrieved successfully.');
    }

    /**
     * Staff directory for assignments.
     */
    public function staff(Request $request): JsonResponse
    {
        $query = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['marketing', 'consultant', 'application', 'admin', 'super-admin'])
              ->orWhereIn('name', ['Marketing', 'Consultant', 'Application', 'Admin', 'Super Admin']);
        });

        if ($roleSlug = $request->get('role')) {
            $query->whereHas('roles', fn($q) => $q->where('slug', $roleSlug)->orWhere('name', $roleSlug));
        }

        $staff = $query->with('roles:id,name,slug')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'username', 'designation']);

        return $this->sendResponse($staff, 'Staff directory retrieved successfully.');
    }
}
