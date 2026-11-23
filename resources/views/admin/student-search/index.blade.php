@extends('admin.layouts.master')

@section('title', 'Search Students')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select2.css') }}">
    <style>
        .student-search-filter-row .nice-select {
            width: 100%;
            height: 38px !important;
            line-height: 36px !important;
            display: flex !important;
            align-items: center !important;
            background-image: none !important;
            padding-left: 12px;
            padding-right: 28px;
            border-radius: 6px;
            border: 1px solid #e0e6ed;
            font-size: 0.875rem;
        }
        .student-search-filter-row .nice-select .current {
            display: block;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .student-search-filter-row .nice-select .nice-select-dropdown {
            z-index: 60 !important;
            min-width: 240px;
            width: 100%;
        }
        .student-search-filter-row .nice-select .list {
            width: 100%;
            max-height: 260px;
        }
        .dark .student-search-filter-row .nice-select {
            background-color: #121e32;
            border-color: #17263c;
            color: #888ea8;
        }
        .dark .student-search-filter-row .nice-select .nice-select-dropdown {
            background-color: #121e32;
            border: 1px solid #1b2e4b;
        }
        .dark .student-search-filter-row .nice-select .nice-select-search {
            background-color: #0e1726;
            border-color: #1b2e4b;
            color: #e2e8f0;
        }
        .dark .student-search-filter-row .nice-select .option:hover,
        .dark .student-search-filter-row .nice-select .option.focus,
        .dark .student-search-filter-row .nice-select .option.selected.focus {
            background-color: #1b2e4b;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')
<div x-data="studentSearchComponent()">
    <!-- Page Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold uppercase">Search Students</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Directory & search across personal, academic, parental, financial & country-specific remarks.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.student-payment-statuses.index') }}" class="btn btn-outline-primary btn-sm text-xs font-semibold gap-1.5" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                Manage Payment Statuses
            </a>
        </div>
    </div>

    <!-- Filter Panel (2 Rows) -->
    <div class="panel mt-5">
        <form action="{{ route('admin.student-search.index') }}" method="GET" id="student_search_filter_form">
            <div style="display: flex; flex-direction: column; gap: 12px; width: 100%;">
                
                <!-- Row 1: Keyword, Country, Remarks -->
                <div class="student-search-filter-row" style="display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap; width: 100%;">
                    <!-- Search Keyword -->
                    <div style="flex: 2; min-width: 200px;">
                        <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Keyword Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search name, phone, email, passport, parent..."
                            class="form-input text-sm w-full" style="height: 38px;" />
                    </div>

                    <!-- Country Filter -->
                    <div style="flex: 1.2; min-width: 140px;">
                        <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Country</label>
                        <select name="country_id" id="filter_country_id" class="form-select text-sm w-full" style="height: 38px;">
                            <option value="">All Countries</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ request('country_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Remarks Filter (Dynamic based on Country) -->
                    <div style="flex: 1.5; min-width: 170px;">
                        <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Remarks</label>
                        <select name="remarks" id="filter_remarks" class="form-select text-sm w-full" style="height: 38px;">
                            <option value="">{{ !empty($activeCountryName) ? 'All Remarks (' . $activeCountryName . ')' : 'All Remarks' }}</option>
                            @php
                                $displayRemarks = !empty($filterRemarks) ? $filterRemarks : ($allRemarks ?? []);
                            @endphp
                            @foreach($displayRemarks as $rKey => $rLabel)
                                <option value="{{ $rLabel }}" {{ request('remarks') == $rLabel ? 'selected' : '' }}>
                                    {{ $rLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Row 2: University, Payment Status, Action Buttons -->
                <div class="student-search-filter-row" style="display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap; width: 100%;">
                    <!-- University Filter (Searchable) -->
                    <div style="flex: 2; min-width: 200px;">
                        <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">University</label>
                        <select name="university_id" id="filter_university_id" class="form-select text-sm w-full" style="height: 38px;">
                            <option value="">All Universities</option>
                            @foreach($universities as $u)
                                <option value="{{ $u->id }}" {{ request('university_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Status -->
                    <div style="flex: 1.2; min-width: 140px;">
                        <div class="flex items-center justify-between mb-1" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300" style="margin-bottom: 0;">Payment Status</label>
                            <button type="button" @click="openPaymentStatusModal()" class="text-[11px] font-semibold text-primary hover:underline flex items-center gap-0.5" style="display: inline-flex; align-items: center; gap: 2px;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Status
                            </button>
                        </div>
                        <select name="payment_status" id="filter_payment_status" class="form-select text-sm w-full" style="height: 38px;">
                            <option value="">All Status</option>
                            @foreach($paymentStatuses as $st)
                                <option value="{{ $st->code }}" {{ request('payment_status') == $st->code ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 8px; white-space: nowrap;">
                        <button type="submit" class="btn btn-primary gap-1.5 px-4 text-xs font-semibold" style="height: 38px;">
                            Filter
                        </button>
                        <a href="{{ route('admin.student-search.index') }}" class="btn btn-outline-danger px-3 text-xs font-semibold" style="height: 38px; display: inline-flex; align-items: center;">Reset</a>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Results Data Table -->
    <div class="panel mt-5 p-0">
        <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
            <table class="table-hover w-full table-auto text-left text-sm">
                <thead class="bg-gray-50 dark:bg-[#1b2e4b] text-xs uppercase text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-3">Contact</th>
                        <th class="py-3 px-3">Passport</th>
                        <th class="py-3 px-3">Destination & Course</th>
                        <th class="py-3 px-3">Parents & Sponsor</th>
                        <th class="py-3 px-3">Payments</th>
                        <th class="py-3 px-3 min-w-[200px]">Remarks (Country Based)</th>
                        <th class="py-3 px-3 min-w-[160px]">Notes</th>
                        <th class="py-3 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($students as $student)
                        @php
                            $countryName = strtolower(trim($student->country?->name ?? ''));
                            $remarksOptions = $student->available_remarks;
                            $paid = (float) ($student->payments_sum_amount ?? $student->total_paid_amount);
                            $package = (float) ($student->package_amount ?? 0);
                            $due = max(0, $package - $paid);
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-[#1a2941] transition-colors" id="student-row-{{ $student->id }}">
                            <!-- Student Info -->
                            <td class="py-3 px-4">
                                <div class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-primary/10 text-primary font-bold text-xs">
                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="hover:text-primary hover:underline">
                                            {{ $student->full_name }}
                                        </a>
                                        <div class="text-[11px] text-gray-400 font-normal">ID: #{{ $student->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="py-3 px-3">
                                <div class="font-medium text-gray-800 dark:text-gray-200">
                                    <a href="tel:{{ $student->phone }}" class="hover:text-primary flex items-center gap-1">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary shrink-0">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                        </svg>
                                        {{ $student->phone }}
                                    </a>
                                </div>
                                @if($student->email)
                                    <div class="text-[12px] text-gray-500 dark:text-gray-400 truncate max-w-[160px]" title="{{ $student->email }}">
                                        {{ $student->email }}
                                    </div>
                                @endif
                            </td>

                            <!-- Passport -->
                            <td class="py-3 px-3">
                                @if($student->passport_number)
                                    <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                        {{ $student->passport_number }}
                                    </span>
                                    @if($student->passport_validity)
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            Exp: {{ $student->passport_validity->format('d M Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">Not recorded</span>
                                @endif
                            </td>

                            <!-- Destination & Academic -->
                            <td class="py-3 px-3">
                                <div>
                                    @if($student->country)
                                        <span class="badge badge-outline-primary text-[11px] px-2 py-0.5 font-semibold">
                                            {{ $student->country->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">N/A</span>
                                    @endif
                                </div>
                                <div class="font-medium text-xs text-gray-800 dark:text-gray-200 mt-1">
                                    {{ Str::limit($student->university?->name, 20) ?: 'No University' }}
                                </div>
                                @if($student->subject || $student->course)
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ Str::limit($student->subject, 20) ?: Str::limit($student->course?->name, 20) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Parents & Sponsor -->
                            <td class="py-3 px-3">
                                <div class="text-xs">
                                    @if($student->father_name)
                                        <div><span class="text-gray-400">Father:</span> <span class="font-medium text-gray-800 dark:text-gray-200">{{ $student->father_name }}</span></div>
                                    @endif
                                    @if($student->mother_name)
                                        <div><span class="text-gray-400">Mother:</span> <span class="font-medium text-gray-800 dark:text-gray-200">{{ $student->mother_name }}</span></div>
                                    @endif
                                    @if($student->sponsor_phone)
                                        <div class="mt-0.5 text-[11px] text-primary flex items-center gap-1 font-mono">
                                            <span>Sponsor:</span>
                                            <a href="tel:{{ $student->sponsor_phone }}" class="hover:underline">{{ $student->sponsor_phone }}</a>
                                        </div>
                                    @endif
                                    @if(!$student->father_name && !$student->mother_name && !$student->sponsor_phone)
                                        <span class="text-xs text-gray-400 italic">No parent info</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Payments & Dynamic Status Dropdown -->
                            <td class="py-3 px-3">
                                <div class="text-xs" style="min-width: 140px;">
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span><span class="text-gray-400">Pkg:</span> <span class="font-semibold">{{ number_format($package, 0) }}</span></span>
                                        <span><span class="text-gray-400">Paid:</span> <span class="text-success font-semibold">{{ number_format($paid, 0) }}</span></span>
                                    </div>
                                    <div class="relative">
                                        <select class="form-select text-[11px] py-1 px-2 pr-6 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-[#121e32] rounded student-payment-status-select font-medium"
                                            @change="updatePaymentStatus({{ $student->id }}, $event.target.value)">
                                            @foreach($paymentStatuses as $st)
                                                <option value="{{ $st->code }}" {{ $student->calculated_payment_status == $st->code ? 'selected' : '' }}>
                                                    {{ $st->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="payment-status-notify-{{ $student->id }}" class="text-[10px] mt-0.5 hidden text-success font-semibold flex items-center gap-1">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            Saved
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Dynamic Remarks Dropdown -->
                            <td class="py-3 px-3">
                                <div class="relative">
                                    <select class="form-select text-xs py-1.5 pr-8 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-[#121e32]"
                                        @change="updateRemarks({{ $student->id }}, $event.target.value)">
                                        <option value="">-- Select Remark --</option>
                                        @foreach($remarksOptions as $rKey => $rLabel)
                                            <option value="{{ $rLabel }}" {{ $student->remarks == $rLabel ? 'selected' : '' }}>
                                                {{ $rLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="remark-status-{{ $student->id }}" class="text-[11px] mt-0.5 hidden text-success font-semibold flex items-center gap-1">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Saved
                                    </div>
                                </div>
                            </td>

                            <!-- Notes Field -->
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-1.5" style="display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                                    <span class="text-xs text-gray-700 dark:text-gray-300" id="notes-text-{{ $student->id }}" style="display: inline-block; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; line-height: 1.25;">
                                        {{ Str::limit($student->notes, 15) ?: 'N/A' }}
                                    </span>
                                    <button type="button"
                                        @click="openNotesModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', `{{ addslashes($student->notes ?? '') }}`)"
                                        class="text-primary hover:text-primary-dark p-1 rounded hover:bg-primary/10 shrink-0"
                                        style="display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;"
                                        title="Edit note">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: block;">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                        <li>
                                            <a href="{{ route('admin.students.show', $student->id) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                View
                                            </a>
                                        </li>
                                        @can('*consultant')
                                            <li>
                                                <a href="{{ route('admin.students.edit', $student->id) }}" @click="toggle">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this student?');" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-danger hover:!bg-danger/10 hover:!text-danger">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-danger" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-12 text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8" stroke-width="1.5"></circle>
                                        <path d="M21 21L16.65 16.65" stroke-width="1.5" stroke-linecap="round"></path>
                                    </svg>
                                    <p class="font-semibold text-base">No students matched your search criteria.</p>
                                    <p class="text-xs text-gray-400 mt-1">Try modifying your filters or clearing keyword terms.</p>
                                    <a href="{{ route('admin.student-search.index') }}" class="btn btn-sm btn-primary mt-3">Reset Filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <!-- Notes Modal -->
    <template x-teleport="#modal-portal">
        <div x-cloak
            x-show="isNotesModalOpen"
            class="modal-overlay-full"
            style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="isNotesModalOpen = false"
                    x-show="isNotesModalOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h3 class="text-base font-bold text-dark dark:text-white-light">
                            Edit Note: <span class="text-primary" x-text="activeStudentName"></span>
                        </h3>
                        <button type="button" @click="isNotesModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div class="p-5">
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">Notes / Remarks Details</label>
                            <textarea x-model="activeStudentNotes"
                                rows="5"
                                class="form-textarea w-full text-sm rounded border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#121e32]"
                                placeholder="Enter counselor notes, visa updates, or special remarks..."></textarea>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-[#e0e6ed] pt-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="isNotesModalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="button" @click="saveNotes()" :disabled="isSavingNotes" class="btn btn-primary px-8">
                                <span x-show="!isSavingNotes">Save Note</span>
                                <span x-show="isSavingNotes">Saving...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Add Payment Status Modal -->
    <template x-teleport="#modal-portal">
        <div x-cloak
            x-show="isPaymentStatusModalOpen"
            class="modal-overlay-full"
            style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="isPaymentStatusModalOpen = false"
                    x-show="isPaymentStatusModalOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="w-full max-w-md rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h3 class="text-base font-bold text-dark dark:text-white-light">
                            Add Payment Status
                        </h3>
                        <button type="button" @click="isPaymentStatusModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="savePaymentStatus()">
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold mb-1 text-slate-700 dark:text-slate-300">
                                    Status Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" x-model="newStatusName" required class="form-input text-xs" placeholder="e.g. Refunded, 1st Installment Paid">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1 text-slate-700 dark:text-slate-300">
                                    Unique Code <span class="text-[10px] text-slate-400">(Optional - auto-generated if blank)</span>
                                </label>
                                <input type="text" x-model="newStatusCode" class="form-input text-xs" placeholder="e.g. refunded">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1 text-slate-700 dark:text-slate-300">Badge Color</label>
                                <select x-model="newStatusColor" class="form-select text-xs">
                                    <option value="info">Info (Blue)</option>
                                    <option value="success">Success (Green)</option>
                                    <option value="warning">Warning (Amber)</option>
                                    <option value="danger">Danger (Red)</option>
                                    <option value="secondary">Secondary (Gray)</option>
                                </select>
                            </div>
                            <template x-if="newStatusError">
                                <div class="text-xs text-danger" x-text="newStatusError"></div>
                            </template>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="isPaymentStatusModalOpen = false" class="btn btn-outline-danger btn-sm text-xs">Cancel</button>
                            <button type="submit" :disabled="isSavingPaymentStatus" class="btn btn-primary btn-sm text-xs">
                                <span x-show="!isSavingPaymentStatus">Save & Add Status</span>
                                <span x-show="isSavingPaymentStatus">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script src="{{ asset('assets/js/nice-select2.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uniSelect = document.getElementById('filter_university_id');
    if (uniSelect && typeof NiceSelect !== 'undefined') {
        NiceSelect.bind(uniSelect, {
            searchable: true,
            placeholder: 'All Universities',
            searchtext: 'Search university...'
        });
    }

    // Dynamic Country -> Remarks filter dropdown
    const remarksByCountry = @json($remarksByCountry ?? []);
    const allRemarksList = @json(array_values($allRemarks ?? []));
    const countrySelect = document.getElementById('filter_country_id');
    const remarksSelect = document.getElementById('filter_remarks');
    const selectedRemark = @json(request('remarks', ''));

    if (countrySelect && remarksSelect) {
        countrySelect.addEventListener('change', async function() {
            const countryId = this.value;
            const countryText = this.options[this.selectedIndex]?.text?.trim() || '';
            const previousVal = remarksSelect.value;
            remarksSelect.innerHTML = '';

            let list = [];
            let placeholder = 'All Remarks';

            if (!countryId) {
                list = allRemarksList;
                placeholder = 'All Remarks';
            } else {
                list = remarksByCountry[countryId] || [];
                placeholder = countryText && countryText !== 'All Countries' ? `All Remarks (${countryText})` : 'All Remarks';

                // If not found in pre-grouped map, fallback to AJAX endpoint
                if (list.length === 0) {
                    try {
                        const res = await fetch(`{{ route('admin.student-search.country-remarks') }}?country_id=${countryId}`);
                        if (res.ok) {
                            const data = await res.json();
                            if (data.remarks) {
                                list = Object.values(data.remarks);
                            }
                        }
                    } catch (err) {
                        console.error('Error fetching country remarks:', err);
                    }
                }
            }

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = placeholder;
            remarksSelect.appendChild(defaultOpt);

            if (list && list.length > 0) {
                list.forEach(remark => {
                    const opt = document.createElement('option');
                    opt.value = remark;
                    opt.textContent = remark;
                    if (previousVal === remark || selectedRemark === remark) {
                        opt.selected = true;
                    }
                    remarksSelect.appendChild(opt);
                });
            }
        });
    }
});

function studentSearchComponent() {
    return {
        // Notes Modal State
        isNotesModalOpen: false,
        activeStudentId: null,
        activeStudentName: '',
        activeStudentNotes: '',
        isSavingNotes: false,

        // Payment Status Modal State
        isPaymentStatusModalOpen: false,
        newStatusName: '',
        newStatusCode: '',
        newStatusColor: 'info',
        newStatusError: '',
        isSavingPaymentStatus: false,

        openPaymentStatusModal() {
            this.newStatusName = '';
            this.newStatusCode = '';
            this.newStatusColor = 'info';
            this.newStatusError = '';
            this.isSavingPaymentStatus = false;
            this.isPaymentStatusModalOpen = true;
        },

        async savePaymentStatus() {
            if (!this.newStatusName.trim()) return;
            this.isSavingPaymentStatus = true;
            this.newStatusError = '';

            try {
                const response = await fetch('{{ route('admin.student-payment-statuses.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.newStatusName,
                        code: this.newStatusCode,
                        color: this.newStatusColor
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    this.newStatusError = data.message || 'Failed to save payment status.';
                    this.isSavingPaymentStatus = false;
                    return;
                }

                // Append new status to filter dropdown
                const filterSelect = document.getElementById('filter_payment_status');
                if (filterSelect && data.status) {
                    const opt = document.createElement('option');
                    opt.value = data.status.code;
                    opt.textContent = data.status.name;
                    filterSelect.appendChild(opt);
                }

                // Append new status to all student payment status dropdowns in table
                document.querySelectorAll('.student-payment-status-select').forEach(sel => {
                    const opt = document.createElement('option');
                    opt.value = data.status.code;
                    opt.textContent = data.status.name;
                    sel.appendChild(opt);
                });

                this.isPaymentStatusModalOpen = false;
                if (typeof window.showMessage === 'function') {
                    window.showMessage(data.message || 'Payment status created successfully.');
                }
            } catch (err) {
                this.newStatusError = 'Network error. Please try again.';
            } finally {
                this.isSavingPaymentStatus = false;
            }
        },

        // Quick AJAX update for student payment status dropdown
        async updatePaymentStatus(studentId, statusValue) {
            const statusEl = document.getElementById(`payment-status-notify-${studentId}`);
            try {
                const response = await fetch(`{{ url('dashboard/search-student') }}/${studentId}/quick-update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ payment_status: statusValue })
                });

                if (response.ok) {
                    if (statusEl) {
                        statusEl.classList.remove('hidden');
                        setTimeout(() => statusEl.classList.add('hidden'), 2500);
                    }
                } else {
                    alert('Failed to update payment status. Please try again.');
                }
            } catch (error) {
                console.error('Error updating payment status:', error);
                alert('Network error while updating payment status.');
            }
        },

        // Quick AJAX update for remarks dropdown
        async updateRemarks(studentId, remarkValue) {
            const statusEl = document.getElementById(`remark-status-${studentId}`);
            try {
                const response = await fetch(`{{ url('dashboard/search-student') }}/${studentId}/quick-update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ remarks: remarkValue })
                });

                if (response.ok) {
                    if (statusEl) {
                        statusEl.classList.remove('hidden');
                        setTimeout(() => statusEl.classList.add('hidden'), 2500);
                    }
                } else {
                    alert('Failed to update remark. Please try again.');
                }
            } catch (error) {
                console.error('Error updating remarks:', error);
                alert('Network error while updating remark.');
            }
        },

        openNotesModal(studentId, name, notes) {
            this.activeStudentId = studentId;
            this.activeStudentName = name;
            this.activeStudentNotes = notes;
            this.isNotesModalOpen = true;
        },

        async saveNotes() {
            if (!this.activeStudentId) return;
            this.isSavingNotes = true;

            try {
                const response = await fetch(`{{ url('dashboard/search-student') }}/${this.activeStudentId}/quick-update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ notes: this.activeStudentNotes })
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    const textEl = document.getElementById(`notes-text-${this.activeStudentId}`);
                    if (textEl) {
                        textEl.innerText = this.activeStudentNotes ? this.activeStudentNotes : '—';
                    }
                    this.isNotesModalOpen = false;
                } else {
                    alert('Failed to save note. Please try again.');
                }
            } catch (err) {
                console.error(err);
                alert('Network error saving note.');
            } finally {
                this.isSavingNotes = false;
            }
        }
    };
}
</script>
@endpush
@endsection

