@extends('admin.layouts.master')

@section('title', 'Search Students')

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
    </div>

    <!-- Filter Panel -->
    <div class="panel mt-5">
        <form action="{{ route('admin.student-search.index') }}" method="GET" class="space-y-3">
            <!-- Row 1: Keyword, Country, University -->
            <div class="flex items-end gap-3 w-full" style="display: flex;">
                <!-- Search Keyword -->
                <div style="flex: 2; min-width: 250px;">
                    <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Keyword Search</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search name, phone, email, passport, father, mother, sponsor..."
                            class="form-input text-sm ltr:pr-10 rtl:pl-10 w-full" />
                        <span class="absolute inset-y-0 ltr:right-3 rtl:left-3 flex items-center pointer-events-none text-gray-400">
                        </span>
                    </div>
                </div>

                <!-- Country Filter -->
                <div style="flex: 1; min-width: 150px;">
                    <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Country</label>
                    <select name="country_id" id="filter_country_id"
                        x-model="selectedCountryId"
                        @change="onCountryFilterChange($event.target.value)"
                        class="form-select text-sm w-full">
                        <option value="">All Countries</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}" data-name="{{ strtolower($c->name) }}" {{ request('country_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- University Filter -->
                <div style="flex: 1; min-width: 150px;">
                    <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">University</label>
                    <select name="university_id" class="form-select text-sm w-full">
                        <option value="">All Universities</option>
                        @foreach($universities as $u)
                            <option value="{{ $u->id }}" {{ request('university_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 2: Remarks, Payment Status, Action Buttons -->
            <div class="flex items-end gap-3 w-full pt-1" style="display: flex;">
                <!-- Filter by Remarks -->
                <div style="flex: 1.5; min-width: 200px;">
                    <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">
                        Filter by Remarks <span class="text-gray-400 font-normal" x-text="countryRemarksLabel"></span>
                    </label>
                    <select name="remarks" id="filter_remarks" class="form-select text-sm w-full">
                        <option value="">All Remarks</option>
                        <template x-for="(label, key) in filterRemarksOptions" :key="key">
                            <option :value="label" :selected="currentSelectedRemarks === label" x-text="label"></option>
                        </template>
                    </select>
                </div>

                <!-- Payment Status -->
                <div style="flex: 1; min-width: 160px;">
                    <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Payment Status</label>
                    <select name="payment_status" class="form-select text-sm w-full">
                        <option value="">All Payment Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial Due</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="no_package" {{ request('payment_status') == 'no_package' ? 'selected' : '' }}>No Package Set</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div style="flex: 0 0 auto;" class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary gap-1.5 px-5">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        Filter Results
                    </button>
                    <a href="{{ route('admin.student-search.index') }}" class="btn btn-outline-danger px-4">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Data Table -->
    <div class="panel mt-5 p-0">
        <div class="overflow-x-auto min-h-[280px]">
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
                                    {{ $student->university?->name ?? 'No University' }}
                                </div>
                                @if($student->subject || $student->course)
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ $student->subject ?: $student->course?->name }}
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

                            <!-- Payments -->
                            <td class="py-3 px-3 whitespace-nowrap">
                                <div class="text-xs">
                                    <div><span class="text-gray-400">Package:</span> <span class="font-semibold">{{ number_format($package, 2) }}</span></div>
                                    <div><span class="text-gray-400">Paid:</span> <span class="text-success font-semibold">{{ number_format($paid, 2) }}</span></div>
                                    <div class="mt-0.5">
                                        @if($package <= 0)
                                            <span class="badge bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 text-[10px] px-1.5 py-0.5">No Package</span>
                                        @elseif($due <= 0)
                                            <span class="badge bg-success/15 text-success text-[10px] px-1.5 py-0.5 font-semibold">Fully Paid</span>
                                        @else
                                            <span class="badge bg-danger/15 text-danger text-[10px] px-1.5 py-0.5 font-semibold">
                                                Due: {{ number_format($due, 2) }}
                                            </span>
                                        @endif
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
                                <div class="flex items-start gap-1.5">
                                    <div class="text-xs text-gray-700 dark:text-gray-300 line-clamp-2 max-w-[180px]" id="notes-text-{{ $student->id }}">
                                        {{ $student->notes ?: '—' }}
                                    </div>
                                    <button type="button"
                                        @click="openNotesModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', `{{ addslashes($student->notes ?? '') }}`)"
                                        class="text-primary hover:text-primary-dark p-1 rounded hover:bg-primary/10 shrink-0"
                                        title="Edit note">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                            <td colspan="9" class="text-center py-12 text-gray-500">
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
    <template x-teleport="body">
        <div x-cloak
            x-show="isNotesModalOpen"
            class="fixed inset-0 z-[9999] overflow-y-auto bg-black/60"
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
</div>

@push('scripts')
<script>
function studentSearchComponent() {
    return {
        allRemarksConfig: @json($allRemarksConfig),
        countriesList: @json($countries),
        selectedCountryId: "{{ request('country_id') }}",
        currentSelectedRemarks: "{{ request('remarks') }}",
        filterRemarksOptions: @json($filterRemarks),
        countryRemarksLabel: '',

        // Notes Modal State
        isNotesModalOpen: false,
        activeStudentId: null,
        activeStudentName: '',
        activeStudentNotes: '',
        isSavingNotes: false,

        init() {
            this.updateFilterRemarksList(this.selectedCountryId);
        },

        onCountryFilterChange(countryId) {
            this.selectedCountryId = countryId;
            this.updateFilterRemarksList(countryId);
        },

        updateFilterRemarksList(countryId) {
            if (!countryId) {
                this.filterRemarksOptions = this.allRemarksConfig['others'] || {};
                this.countryRemarksLabel = '(All / Others)';
                return;
            }

            const country = this.countriesList.find(c => String(c.id) === String(countryId));
            if (!country) {
                this.filterRemarksOptions = this.allRemarksConfig['others'] || {};
                this.countryRemarksLabel = '(Others)';
                return;
            }

            const countryKey = country.name.toLowerCase().trim();
            if (this.allRemarksConfig[countryKey]) {
                this.filterRemarksOptions = this.allRemarksConfig[countryKey];
                this.countryRemarksLabel = `(${country.name})`;
            } else {
                this.filterRemarksOptions = this.allRemarksConfig['others'] || {};
                this.countryRemarksLabel = `(Others)`;
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

