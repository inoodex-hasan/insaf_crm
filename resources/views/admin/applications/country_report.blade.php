@extends('admin.layouts.master')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Country-wise Student Applications Report')

@section('content')
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold uppercase tracking-wide text-gray-800 dark:text-white">
                    Country-wise Student Applications Report
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Detailed breakdown of students and applications distributed across countries and partner universities.
                </p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary text-xs">
                    Back to Applications
                </a>
            </div>
        </div>

        {{-- KPI Stat Cards --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
            {{-- Total Applications --}}
            <div class="panel h-full">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="text-primary">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <line x1="10" y1="9" x2="8" y2="9"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full ltr:ml-3 rtl:mr-3">
                        <p class="dark:text-white-light text-2xl font-bold">{{ number_format($totalApplications) }}</p>
                        <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Applications</h5>
                    </div>
                </div>
            </div>

            {{-- Unique Students --}}
            <div class="panel h-full">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="text-success">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full ltr:ml-3 rtl:mr-3">
                        <p class="dark:text-white-light text-2xl font-bold">{{ number_format($uniqueStudents) }}</p>
                        <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Unique Students</h5>
                    </div>
                </div>
            </div>

            {{-- Total Countries --}}
            <div class="panel h-full">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="text-info">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full ltr:ml-3 rtl:mr-3">
                        <p class="dark:text-white-light text-2xl font-bold">{{ number_format($totalCountries) }}</p>
                        <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Destination Countries</h5>
                    </div>
                </div>
            </div>

            {{-- Total Universities --}}
            <div class="panel h-full">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <div class="text-warning">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full ltr:ml-3 rtl:mr-3">
                        <p class="dark:text-white-light text-2xl font-bold">{{ number_format($totalUniversities) }}</p>
                        <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Partner Universities</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Bar --}}
        <div class="panel print:hidden">
            <div class="mb-1">
                <form action="{{ route('admin.applications.country-report') }}" method="GET" class="flex flex-col gap-3 w-full">
                    <div style="display: flex; align-items: center; gap: 8px; width: 100%; flex-wrap: wrap;">
                        {{-- Search --}}
                        <div class="relative" style="flex: 2; min-width: 180px;">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search..." class="form-input ltr:pr-11 rtl:pl-11" style="width: 100%;" />
                            <button type="submit"
                                class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5" />
                                    <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>

                        {{-- From Date --}}
                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                            class="form-input" style="flex: 1; min-width: 140px;" title="From Date" />

                        {{-- To Date --}}
                        <input type="date" name="to_date" value="{{ request('to_date') }}"
                            class="form-input" style="flex: 1; min-width: 140px;" title="To Date" />

                        {{-- Country --}}
                        <select name="country_id" class="form-select" style="flex: 1; min-width: 160px;">
                            <option value="">Country</option>
                            @foreach ($filterCountries as $fc)
                                <option value="{{ $fc->id }}" {{ request('country_id') == $fc->id ? 'selected' : '' }}>
                                    {{ $fc->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Buttons --}}
                        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Filter</button>
                        <a href="{{ route('admin.applications.country-report') }}" class="btn btn-outline-danger" style="white-space: nowrap;">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Country Summary Overview Table --}}
        <div class="panel">
            <div class="mb-4 flex items-center justify-between border-b pb-3 dark:border-[#1b2e4b]">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Country Overview & Market Share
                </h3>
                <span class="badge badge-outline-primary text-xs">{{ $countryGroups->count() }} Countries</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr class="text-xs uppercase text-gray-500 bg-gray-50 dark:bg-[#121e32]">
                            <th class="py-2.5 px-3 text-left">Country</th>
                            <th class="py-2.5 px-3 text-center">Universities</th>
                            <th class="py-2.5 px-3 text-center">Applications</th>
                            <th class="py-2.5 px-3 text-center">Unique Students</th>
                            <th class="py-2.5 px-3 text-left w-1/3">Share of Applications</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1b2e4b] text-xs">
                        @forelse ($countryGroups as $country)
                            <tr>
                                <td class="py-2.5 px-3 font-semibold text-gray-800 dark:text-white">
                                    {{ $country['country_name'] }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-medium">
                                    {{ $country['universities_count'] }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-bold text-primary">
                                    {{ $country['total_applications'] }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ $country['unique_students'] }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700 overflow-hidden">
                                            <div class="bg-primary h-2 rounded-full" style="width: {{ $country['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 w-10 text-right">
                                            {{ $country['percentage'] }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">
                                    No application records found matching the criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Detailed Breakdown: Country -> Universities -> Student Applications --}}
        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800 dark:text-white">
                Detailed University & Student Breakdown by Country
            </h3>

            @forelse ($countryGroups as $country)
                <div x-data="{ open: false }" class="panel border border-gray-200 dark:border-[#1b2e4b] p-0 overflow-hidden">
                    {{-- Country Header Accordion Toggle --}}
                    <div @click="open = !open"
                        class="flex items-center justify-between cursor-pointer bg-gray-50/80 px-4 py-3 border-b dark:bg-[#121e32] dark:border-[#1b2e4b] hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center rounded-lg bg-primary/10 text-primary shrink-0" style="width: 32px; height: 32px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $country['country_name'] }}
                                </h4>
                                <span class="text-[11px] text-gray-500">
                                    {{ $country['universities_count'] }} {{ Str::plural('University', $country['universities_count']) }} |
                                    {{ $country['total_applications'] }} {{ Str::plural('Application', $country['total_applications']) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="badge badge-outline-primary text-xs">
                                {{ $country['total_applications'] }} Applications
                            </span>
                            <span class="badge badge-outline-success text-xs">
                                {{ $country['unique_students'] }} Students
                            </span>
                            <svg width="16" height="16" class="text-gray-500 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Country Body: Universities List --}}
                    <div x-show="open" x-collapse class="p-4 space-y-4">
                        @foreach ($country['universities'] as $uni)
                            <div x-data="{ showStudents: false }" class="rounded-lg border border-gray-200 dark:border-[#1b2e4b] bg-white dark:bg-[#0e1726] p-3 shadow-sm">
                                {{-- University Header Row --}}
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center justify-center rounded-md bg-warning/10 text-warning shrink-0" style="width: 28px; height: 28px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-gray-800 dark:text-white">
                                                {{ $uni['university_name'] }}
                                                @if ($uni['short_name'])
                                                    <span class="text-gray-400 font-normal">({{ $uni['short_name'] }})</span>
                                                @endif
                                            </h5>
                                        </div>
                                    </div>

                                    {{-- Counts and Status Badges --}}
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $uni['total_applications'] }} {{ Str::plural('App', $uni['total_applications']) }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold dark:bg-emerald-900/30 dark:text-emerald-300">
                                            {{ $uni['unique_students'] }} {{ Str::plural('Student', $uni['unique_students']) }}
                                        </span>
                                        @if ($uni['offer_letter_count'] > 0)
                                            <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 font-medium dark:bg-purple-900/30 dark:text-purple-300">
                                                {{ $uni['offer_letter_count'] }} Offer Letter
                                            </span>
                                        @endif
                                        @if ($uni['visa_approved_count'] > 0)
                                            <span class="px-2 py-0.5 rounded bg-green-50 text-green-700 font-medium dark:bg-green-900/30 dark:text-green-300">
                                                {{ $uni['visa_approved_count'] }} Visa Approved
                                            </span>
                                        @endif

                                        {{-- Toggle Student Details --}}
                                        <button type="button" @click="showStudents = !showStudents"
                                            class="btn btn-outline-secondary !h-7 !py-0 !px-2.5 text-[11px] gap-1">
                                            <span x-text="showStudents ? 'Hide Students' : 'View Students ({{ $uni['total_applications'] }})'"></span>
                                            <svg class="h-3.5 w-3.5 transition-transform" :class="showStudents ? 'rotate-180' : ''"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Student Application Records Table (Expandable) --}}
                                <div x-show="showStudents" x-collapse class="mt-3 border-t pt-3 dark:border-[#1b2e4b]">
                                    <div class="overflow-x-auto">
                                        <table class="table-hover w-full text-xs">
                                            <thead>
                                                <tr class="bg-gray-50 dark:bg-[#121e32] text-gray-500 uppercase text-[11px]">
                                                    <th class="py-2 px-3 text-left">App ID</th>
                                                    <th class="py-2 px-3 text-left">Student Name</th>
                                                    <th class="py-2 px-3 text-left">Passport</th>
                                                    <th class="py-2 px-3 text-left">Course / Intake</th>
                                                    <th class="py-2 px-3 text-center">Status</th>
                                                    <th class="py-2 px-3 text-center">Visa Status</th>
                                                    <th class="py-2 px-3 text-center">Date Applied</th>
                                                    <th class="py-2 px-3 text-center print:hidden">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-[#1b2e4b]">
                                                @foreach ($uni['applications'] as $app)
                                                    <tr>
                                                        <td class="py-2 px-3 font-mono font-bold text-primary">
                                                            {{ $app->application_id }}
                                                        </td>
                                                        <td class="py-2 px-3 font-semibold text-gray-800 dark:text-gray-200">
                                                            {{ $app->student ? $app->student->full_name : '-' }}
                                                        </td>
                                                        <td class="py-2 px-3 text-gray-600 dark:text-gray-400">
                                                            {{ $app->student ? ($app->student->passport_number ?? '-') : '-' }}
                                                        </td>
                                                        <td class="py-2 px-3">
                                                            <div class="font-medium text-gray-800 dark:text-gray-200">
                                                                {{ $app->course ? $app->course->name : '-' }}
                                                            </div>
                                                            @if ($app->intake)
                                                                <div class="text-[10px] text-gray-400">
                                                                    {{ $app->intake->intake_name }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="py-2 px-3 text-center">
                                                            @if ($app->status === 'approved')
                                                                <span class="badge badge-outline-success text-[10px]">Approved</span>
                                                            @elseif ($app->status === 'rejected')
                                                                <span class="badge badge-outline-danger text-[10px]">Rejected</span>
                                                            @else
                                                                <span class="badge badge-outline-warning text-[10px]">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2 px-3 text-center">
                                                            @if ($app->visa_status === 'approved')
                                                                <span class="badge bg-green-500 text-white text-[10px]">Visa Approved</span>
                                                            @elseif ($app->visa_status === 'rejected')
                                                                <span class="badge bg-red-500 text-white text-[10px]">Visa Rejected</span>
                                                            @elseif ($app->visa_status === 'applied')
                                                                <span class="badge bg-blue-500 text-white text-[10px]">Visa Applied</span>
                                                            @else
                                                                <span class="badge bg-gray-400 text-white text-[10px]">Not Applied</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2 px-3 text-center text-gray-500">
                                                            {{ $app->created_at ? $app->created_at->format('M d, Y') : '-' }}
                                                        </td>
                                                        <td class="py-2 px-3 text-center print:hidden">
                                                            <a href="{{ route('admin.applications.show', $app->id) }}"
                                                                class="btn btn-primary !h-6 !px-2 !py-0 text-[10px]" target="_blank">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="panel py-10 text-center text-gray-500">
                    No application records match your filter criteria.
                </div>
            @endforelse
        </div>
    </div>
@endsection
