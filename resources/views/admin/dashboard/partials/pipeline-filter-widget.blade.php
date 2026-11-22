{{-- Application & Admission Pipeline Intelligence Filter Widget --}}
<div class="panel p-5 bg-white dark:bg-[#1b2e4b] rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] mb-6 shadow-sm">

    {{-- Filter Form Toolbar --}}
    <form id="pipeline-filter-form" action="{{ route('admin.dashboard') }}" method="GET">

        {{-- Top Filter Inputs: Date Range, Intake, Year, Countries --}}
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; width: 100%;">
            
            {{-- 1. Date Range Picker --}}
            <div style="flex: 1.5; min-width: 220px; position: relative;">
                <input type="text"
                       id="pipeline_date_range"
                       name="date_range"
                       value="{{ $dateRange ?? '' }}"
                       placeholder="DD/MM/YYYY - DD/MM/YYYY"
                       class="form-input text-xs w-full"
                       style="height: 38px; border-radius: 6px; padding-right: 32px; {{ !empty($dateRange) ? 'border-color: #16a34a; box-shadow: 0 0 0 1px #16a34a;' : '' }}"
                       autocomplete="off" />
                @if(!empty($dateRange))
                    <button type="button"
                            id="clear-pipeline-date"
                            onclick="clearPipelineDateRange()"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; font-size: 14px; font-weight: bold; padding: 2px 6px;"
                            title="Clear date range">
                        &times;
                    </button>
                @endif
            </div>

            {{-- 2. Intake Dropdown --}}
            <div style="flex: 1; min-width: 150px;">
                <select name="intake" id="pipeline_intake" class="form-select text-xs w-full" style="height: 38px; border-radius: 6px;">
                    <option value="">Intake</option>
                    @foreach($pipelineIntakes as $intakeOption)
                        <option value="{{ $intakeOption }}" {{ ($intakeFilter ?? '') == $intakeOption ? 'selected' : '' }}>
                            {{ $intakeOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Year Dropdown --}}
            <div style="flex: 1; min-width: 130px;">
                <select name="year" id="pipeline_year" class="form-select text-xs w-full" style="height: 38px; border-radius: 6px;">
                    <option value="">Year</option>
                    @foreach($pipelineYears as $yOption)
                        <option value="{{ $yOption }}" {{ ($yearFilter ?? '') == (string)$yOption ? 'selected' : '' }}>
                            {{ $yOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Countries Dropdown --}}
            <div style="flex: 1.2; min-width: 160px;">
                <select name="country_id" id="pipeline_country" class="form-select text-xs w-full" style="height: 38px; border-radius: 6px;">
                    <option value="">Countries</option>
                    @foreach($pipelineCountries as $cOption)
                        <option value="{{ $cOption->id }}" {{ ($countryFilter ?? '') == (string)$cOption->id ? 'selected' : '' }}>
                            {{ $cOption->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- Second Row: Action Buttons --}}
        <div style="display: flex; align-items: center; gap: 8px; margin-top: 14px;">
            <button type="submit"
                    class="btn btn-sm btn-outline-success font-medium text-xs transition"
                    style="border: 1px solid #16a34a; color: #16a34a; border-radius: 6px; padding: 6px 16px; background-color: transparent;">
                Apply Filters
            </button>

            @if(!empty($dateRange) || !empty($intakeFilter) || !empty($yearFilter) || !empty($countryFilter))
                <a href="{{ route('admin.dashboard') }}"
                   class="btn btn-sm btn-outline-danger font-medium text-xs transition"
                   style="border-radius: 6px; padding: 6px 14px;">
                    Reset Filters
                </a>
            @endif
        </div>

    </form>

    {{-- KPI Cards: 4 Cards per line (4x2 Responsive Grid with Colored Left Accent Borders) --}}
    <div class="pipeline-kpi-grid">

        {{-- Card 1: All Applications (Blue Left Border with Details Dropdown) --}}
        <div x-data="{ open: false }" style="position: relative;">
            <div @click="open = !open"
                 class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
                 style="display: block; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #2563eb; padding: 14px 18px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition">
                        All Applications
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition transform duration-200"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px;">
                    {{ number_format($pipelineMetrics['all_applications'] ?? 0) }}
                </div>
            </div>

            {{-- Applications Details Dropdown Menu --}}
            <div x-show="open"
                 @click.away="open = false"
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 style="position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 100; min-width: 250px; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); overflow: hidden;"
                 class="dark:bg-[#1b2e4b] dark:border-[#2e405b]">
                <div class="py-1 divide-y divide-gray-100 dark:divide-[#2e405b]">
                    {{-- 1. All Applications --}}
                    <a href="{{ route('admin.applications.index', array_filter(['country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-slate-800 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-600 inline-block"></span>
                            All Applications
                        </span>
                        <span class="font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/40 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['applications']['total'] ?? $pipelineMetrics['all_applications'] ?? 0) }}
                        </span>
                    </a>

                    {{-- 2. Application Pending --}}
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'pending', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-500 inline-block"></span>
                            Application Pending
                        </span>
                        <span class="font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['applications']['pending'] ?? 0) }}
                        </span>
                    </a>

                    {{-- 3. Application Applied / Processing --}}
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'applied', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-indigo-500 inline-block"></span>
                            Application Applied / Processing
                        </span>
                        <span class="font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['applications']['applied'] ?? 0) }}
                        </span>
                    </a>

                    {{-- 4. Application Cancelled / Withdrawn --}}
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'cancelled', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-rose-500 inline-block"></span>
                            Application Cancelled / Withdrawn
                        </span>
                        <span class="font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['applications']['cancelled'] ?? 0) }}
                        </span>
                    </a>

                    {{-- 5. Application Rejected --}}
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'rejected', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-red-600 inline-block"></span>
                            Application Rejected
                        </span>
                        <span class="font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['applications']['rejected'] ?? 0) }}
                        </span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 2: Offers (Blue Left Border with Details Dropdown) --}}
        <div x-data="{ open: false }" style="position: relative;">
            <div @click="open = !open"
                 class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
                 style="display: block; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #2563eb; padding: 14px 18px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition">
                        Offers
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition transform duration-200"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px;">
                    {{ number_format($pipelineMetrics['offers'] ?? 0) }}
                </div>
            </div>

            {{-- Offers Details Dropdown Menu --}}
            <div x-show="open"
                 @click.away="open = false"
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 style="position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 100; min-width: 250px; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); overflow: hidden;"
                 class="dark:bg-[#1b2e4b] dark:border-[#2e405b]">
                <div class="py-1 divide-y divide-gray-100 dark:divide-[#2e405b]">
                    {{-- 1. Total Offers --}}
                    <a href="{{ route('admin.applications.index', array_filter(['offer_letter' => 1, 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-slate-800 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-600 inline-block"></span>
                            Total Offers
                        </span>
                        <span class="font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/40 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['offers']['total'] ?? $pipelineMetrics['offers'] ?? 0) }}
                        </span>
                    </a>

                    {{-- 2. Offer Letter Received --}}
                    <a href="{{ route('admin.applications.index', array_filter(['offer_letter' => 1, 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 inline-block"></span>
                            Offer Received
                        </span>
                        <span class="font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['offers']['received'] ?? 0) }}
                        </span>
                    </a>

                    {{-- 3. Conditional Offer --}}
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'conditional_offer', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-500 inline-block"></span>
                            Conditional Offer
                        </span>
                        <span class="font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['offers']['conditional'] ?? 0) }}
                        </span>
                    </a>

                    {{-- 4. Unconditional Offer --}}
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'unconditional_offer', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-teal-500 inline-block"></span>
                            Unconditional Offer
                        </span>
                        <span class="font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['offers']['unconditional'] ?? 0) }}
                        </span>
                    </a>

                    {{-- 5. Offer Pending / Waiting --}}
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'pending', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
                       class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#253b5c] transition">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-gray-400 inline-block"></span>
                            Offer Pending / Waiting
                        </span>
                        <span class="font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">
                            {{ number_format($pipelineDetails['offers']['waiting'] ?? 0) }}
                        </span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 3: Payments (Green Left Border) --}}
        <a href="{{ route('admin.applications.index', array_filter(['final_payment' => 1, 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
           class="group transition hover:-translate-y-0.5 hover:shadow-md"
           style="display: block; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #16a34a; padding: 14px 18px; text-decoration: none;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-success transition">
                    Payments
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px;">
                {{ number_format($pipelineMetrics['payments'] ?? 0) }}
            </div>
        </a>

        {{-- Card 4: Visa Received (Green Left Border) --}}
        <a href="{{ route('admin.applications.index', array_filter(['visa_status' => 'approved', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
           class="group transition hover:-translate-y-0.5 hover:shadow-md"
           style="display: block; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #16a34a; padding: 14px 18px; text-decoration: none;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-success transition">
                    Visa Received
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px;">
                {{ number_format($pipelineMetrics['visa_received'] ?? 0) }}
            </div>
        </a>

        {{-- Card 5: Visa Rejected (Red Left Border) --}}
        <a href="{{ route('admin.applications.index', array_filter(['visa_status' => 'rejected', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
           class="group transition hover:-translate-y-0.5 hover:shadow-md"
           style="display: block; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #dc2626; padding: 14px 18px; text-decoration: none;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-danger transition">
                    Visa Rejected
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px;">
                {{ number_format($pipelineMetrics['visa_rejected'] ?? 0) }}
            </div>
        </a>

        {{-- Card 6: Non-Enrolment (Orange Left Border) --}}
        <a href="{{ route('admin.applications.index', array_filter(['status' => 'withdrawn', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
           class="group transition hover:-translate-y-0.5 hover:shadow-md"
           style="display: block; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #f59e0b; padding: 14px 18px; text-decoration: none;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-warning transition">
                    Non-Enrolment
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px;">
                {{ number_format($pipelineMetrics['non_enrolment'] ?? 0) }}
            </div>
        </a>

        {{-- Card 7: Deferrals (Orange Left Border) --}}
        <a href="{{ route('admin.applications.index', array_filter(['status' => 'deferred', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
           class="group transition hover:-translate-y-0.5 hover:shadow-md"
           style="display: block; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #f59e0b; padding: 14px 18px; text-decoration: none;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-warning transition">
                    Deferrals
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px;">
                {{ number_format($pipelineMetrics['deferrals'] ?? 0) }}
            </div>
        </a>

        {{-- Card 8: Pending from Partner (Yellow Left Border) --}}
        <a href="{{ route('admin.applications.index', array_filter(['status' => 'pending', 'country_id' => $countryFilter, 'intake' => $intakeFilter, 'year' => $yearFilter])) }}"
           class="group transition hover:-translate-y-0.5 hover:shadow-md"
           style="display: block; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #eab308; padding: 14px 18px; text-decoration: none;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-yellow-600 transition">
                    Pending from Partner
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px;">
                {{ number_format($pipelineMetrics['pending_partner'] ?? 0) }}
            </div>
        </a>

    </div>

</div>

{{-- Flatpickr Styles & Scripts for Range Mode --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/flatpickr.min.css') }}">
<style>
    .pipeline-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-top: 20px;
    }
    @media (max-width: 1023px) {
        .pipeline-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 639px) {
        .pipeline-kpi-grid {
            grid-template-columns: 1fr;
        }
    }
    .dark [style*="background: #ffffff"] {
        background: #1b2e4b !important;
        border-color: #1b2e4b !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/flatpickr.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('pipeline_date_range');
    if (dateInput && typeof flatpickr !== 'undefined') {
        flatpickr(dateInput, {
            mode: "range",
            dateFormat: "d/m/Y",
            allowInput: true,
            onClose: function(selectedDates, dateStr, instance) {
                // If clear button should be shown
            }
        });
    }
});

function clearPipelineDateRange() {
    const dateInput = document.getElementById('pipeline_date_range');
    if (dateInput) {
        if (dateInput._flatpickr) {
            dateInput._flatpickr.clear();
        } else {
            dateInput.value = '';
        }
        document.getElementById('pipeline-filter-form').submit();
    }
}
</script>
@endpush
