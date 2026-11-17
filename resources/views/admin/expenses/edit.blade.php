@extends('admin.layouts.master')

@section('title', 'Edit Expense #' . $expense->id)

@section('content')

@php
    $tourCategory = $categories->firstWhere('code', '51006') ?? $categories->first(fn($c) => str_contains(strtolower($c->name), 'tour') || str_contains(strtolower($c->name), 'invest'));
@endphp

<div x-data="expenseEditFormHandler()">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wider text-dark dark:text-white-light">
                Edit Expense #{{ $expense->id }}
            </h2>
            <p class="text-xs text-white-dark mt-1">Update expense records, accounts, or delegation information.</p>
        </div>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Expenses
        </a>
    </div>

    {{-- Main Form Panel --}}
    <div class="panel p-6 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#1b2e4b]">
        <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. Business Tour / Staff Investment Checkbox --}}
            <div class="p-4 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] bg-gray-50 dark:bg-[#121e32]">
                <label class="flex items-start gap-3 cursor-pointer select-none mb-0">
                    <input type="checkbox" name="is_investment" value="1"
                           class="form-checkbox text-primary w-5 h-5 rounded mt-0.5"
                           x-model="form.is_investment"
                           @change="onInvestmentToggle()">
                    <div>
                        <span class="font-bold text-sm text-dark dark:text-white-light">Business Tour / Staff Investment</span>
                        <p class="text-xs text-white-dark mt-0.5">
                            Check when spending on sending office staff to universities abroad for negotiation, partnership, or official visits.
                        </p>
                    </div>
                </label>
            </div>

            {{-- 2. Delegation & Destination Details (Visible when is_investment is checked) --}}
            <div x-show="form.is_investment" x-transition.duration.250ms
                 class="p-5 rounded-xl bg-gray-50/50 dark:bg-[#121e32]/50 border border-[#e0e6ed] dark:border-[#1b2e4b] space-y-4">
                <h5 class="text-xs font-bold uppercase text-dark dark:text-white-light tracking-wider border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-2">
                    Delegation Details
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Staff Member with Searchbar --}}
                    <div class="form-group">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Staff Member <span class="text-danger">*</span>
                        </label>
                        <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                            <input type="hidden" name="staff_id" :value="form.staff_id" :required="form.is_investment">
                            <button type="button" @click="open = !open"
                                    class="form-input text-left flex items-center justify-between">
                                <span x-text="selectedStaffName() || 'Select Staff Member'"></span>
                                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg shadow-lg p-2 max-h-60 overflow-hidden flex flex-col">
                                <input type="text" x-model="search"
                                       class="form-input text-xs mb-2 py-1.5 px-2.5">
                                <div class="overflow-y-auto max-h-48 space-y-1">
                                    <template x-for="staff in filteredStaffList(search)" :key="staff.id">
                                        <div @click="form.staff_id = staff.id; open = false; search = ''"
                                             class="px-3 py-1.5 text-xs rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-[#121e32]"
                                             :class="form.staff_id == staff.id ? 'bg-primary/10 text-primary font-bold' : ''"
                                             x-text="staff.name">
                                        </div>
                                    </template>
                                    <div x-show="filteredStaffList(search).length === 0" class="text-xs text-white-dark p-2 text-center">
                                        No staff found
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tour Purpose --}}
                    <div class="form-group">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Purpose
                        </label>
                        <input type="text" name="investment_purpose" list="tour_purposes" class="form-input"
                               x-model="form.investment_purpose">
                        <datalist id="tour_purposes">
                            <option value="Partnership Negotiation">
                            <option value="Agreement Signing">
                            <option value="Spot Admission">
                            <option value="University Visit">
                            <option value="Education Fair">
                            <option value="Air Ticket & Travel">
                            <option value="Accommodation & Allowance">
                        </datalist>
                    </div>

                    {{-- Destination Country with Searchbar --}}
                    <div class="form-group">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Destination Country
                        </label>
                        <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                            <input type="hidden" name="country_id" :value="form.country_id">
                            <button type="button" @click="open = !open"
                                    class="form-input text-left flex items-center justify-between">
                                <span x-text="selectedCountryName() || 'Select Country'"></span>
                                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg shadow-lg p-2 max-h-60 overflow-hidden flex flex-col">
                                <input type="text" x-model="search"
                                       class="form-input text-xs mb-2 py-1.5 px-2.5">
                                <div class="overflow-y-auto max-h-48 space-y-1">
                                    <div @click="setCountry(''); open = false; search = ''"
                                         class="px-3 py-1.5 text-xs rounded cursor-pointer text-white-dark hover:bg-gray-100 dark:hover:bg-[#121e32]">
                                        None / Clear
                                    </div>
                                    <template x-for="c in filteredCountryList(search)" :key="c.id">
                                        <div @click="setCountry(c.id); open = false; search = ''"
                                             class="px-3 py-1.5 text-xs rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-[#121e32]"
                                             :class="form.country_id == c.id ? 'bg-primary/10 text-primary font-bold' : ''"
                                             x-text="c.name">
                                        </div>
                                    </template>
                                    <div x-show="filteredCountryList(search).length === 0" class="text-xs text-white-dark p-2 text-center">
                                        No countries found
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Target University with Searchbar (strictly filtered by country) --}}
                    <div class="form-group">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Target University
                        </label>
                        <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                            <input type="hidden" name="university_id" :value="form.university_id">
                            <button type="button" @click="open = !open"
                                    class="form-input text-left flex items-center justify-between">
                                <span x-text="selectedUniversityName() || (form.country_id ? 'Select University' : 'Select Country First')"></span>
                                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg shadow-lg p-2 max-h-60 overflow-hidden flex flex-col">
                                <input type="text" x-model="search"
                                       class="form-input text-xs mb-2 py-1.5 px-2.5">
                                <div class="overflow-y-auto max-h-48 space-y-1">
                                    <div x-show="!form.country_id" class="text-xs text-white-dark p-3 text-center">
                                        Please select a country first
                                    </div>
                                    <div x-show="form.country_id"
                                         @click="form.university_id = ''; open = false; search = ''"
                                         class="px-3 py-1.5 text-xs rounded cursor-pointer text-white-dark hover:bg-gray-100 dark:hover:bg-[#121e32]">
                                        None / General Visit
                                    </div>
                                    <template x-for="uni in filteredUniversitiesBySearch(search)" :key="uni.id">
                                        <div @click="form.university_id = uni.id; open = false; search = ''"
                                             class="px-3 py-1.5 text-xs rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-[#121e32]"
                                             :class="form.university_id == uni.id ? 'bg-primary/10 text-primary font-bold' : ''"
                                             x-text="uni.name">
                                        </div>
                                    </template>
                                    <div x-show="form.country_id && filteredUniversitiesBySearch(search).length === 0" class="text-xs text-white-dark p-2 text-center">
                                        No universities found for selected country
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Core Financial & Expense Details --}}
            <div class="space-y-4">
                <h5 class="text-xs font-bold uppercase text-dark dark:text-white-light tracking-wider border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-2">
                    Payment Details
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- Description --}}
                    <div class="form-group md:col-span-2 lg:col-span-3">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Description <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="description" class="form-input" required x-model="form.description">
                    </div>

                    {{-- Amount --}}
                    <div class="form-group">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Amount (BDT) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-input font-bold" required
                               x-model="form.amount">
                    </div>

                    {{-- Expense Date --}}
                    <div class="form-group">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Expense Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="expense_date" class="form-input" required x-model="form.expense_date">
                    </div>

                    {{-- Category (Chart of Account) - READ ONLY when is_investment is checked --}}
                    <div class="form-group">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Category (Chart of Account) <span class="text-danger">*</span>
                        </label>

                        {{-- Read-only display when Business Tour is checked --}}
                        <div x-show="form.is_investment">
                            <input type="text" class="form-input bg-gray-100 dark:bg-black/20 text-gray-700 dark:text-gray-300 cursor-not-allowed font-medium"
                                   :value="tourCategoryName" readonly disabled>
                            <input type="hidden" name="chart_of_account_id" :value="tourCategoryId" :disabled="!form.is_investment">
                        </div>

                        {{-- Editable select when Business Tour is unchecked --}}
                        <div x-show="!form.is_investment">
                            <select name="chart_of_account_id" class="form-select font-medium"
                                    x-model="form.chart_of_account_id" :required="!form.is_investment" :disabled="form.is_investment">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('chart_of_account_id', $expense->chart_of_account_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="form-group">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Payment Method <span class="text-danger">*</span>
                        </label>
                        <select name="payment_method" class="form-select" required x-model="form.payment_method">
                            <option value="cash" {{ old('payment_method', $expense->payment_method) === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="mobile_banking" {{ old('payment_method', $expense->payment_method) === 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
                            <option value="cheque" {{ old('payment_method', $expense->payment_method) === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="credit" {{ old('payment_method', $expense->payment_method) === 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>

                    {{-- Pay From Account --}}
                    <div class="form-group md:col-span-2">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Pay From Office Account
                        </label>
                        <select name="office_account_id" class="form-select" x-model="form.office_account_id">
                            <option value="">Select Office Account</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('office_account_id', $expense->office_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }}
                                    ({{ ucfirst($account->account_type) }}{{ $account->provider_name ? ' - ' . $account->provider_name : '' }} - {{ $account->account_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div class="form-group md:col-span-2 lg:col-span-3">
                        <label class="text-xs font-semibold text-dark dark:text-white-light">
                            Notes
                        </label>
                        <textarea name="notes" rows="3" class="form-textarea" x-model="form.notes"></textarea>
                    </div>
                </div>
            </div>

            {{-- Form Submit Footer --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-danger px-6">Cancel</a>
                <button type="submit" class="btn btn-primary px-10">
                    Update Record
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function expenseEditFormHandler() {
    const allStaff = @js($staffList);
    const allCountries = @js($countries);
    const allUniversities = @js($universities);
    const tourCategory = @js($tourCategory);

    return {
        staffList: allStaff,
        countries: allCountries,
        universities: allUniversities,
        filteredUniversities: [],
        tourCategoryId: tourCategory ? tourCategory.id : '',
        tourCategoryName: tourCategory ? `${tourCategory.name} (${tourCategory.code})` : 'Tour & Partnership Investment',
        form: {
            description: @js(old('description', $expense->description)),
            amount: @js(old('amount', $expense->amount)),
            expense_date: @js(old('expense_date', $expense->expense_date->format('Y-m-d'))),
            chart_of_account_id: @js(old('chart_of_account_id', $expense->chart_of_account_id)),
            payment_method: @js(old('payment_method', $expense->payment_method)),
            office_account_id: @js(old('office_account_id', $expense->office_account_id ?? '')),
            notes: @js(old('notes', $expense->notes ?? '')),
            is_investment: {{ old('is_investment', $expense->is_investment) ? 'true' : 'false' }},
            staff_id: @js(old('staff_id', $expense->staff_id ?? '')),
            country_id: @js(old('country_id', $expense->country_id ?? '')),
            university_id: @js(old('university_id', $expense->university_id ?? '')),
            investment_purpose: @js(old('investment_purpose', $expense->investment_purpose ?? '')),
        },
        init() {
            this.updateFilteredUniversities();
            if (this.form.is_investment && tourCategory) {
                this.form.chart_of_account_id = tourCategory.id;
            }
        },
        selectedStaffName() {
            if (!this.form.staff_id) return '';
            const s = this.staffList.find(item => String(item.id) === String(this.form.staff_id));
            return s ? s.name : '';
        },
        filteredStaffList(search) {
            if (!search || !search.trim()) return this.staffList;
            const term = search.toLowerCase().trim();
            return this.staffList.filter(item => item.name.toLowerCase().includes(term));
        },
        selectedCountryName() {
            if (!this.form.country_id) return '';
            const c = this.countries.find(item => String(item.id) === String(this.form.country_id));
            return c ? c.name : '';
        },
        filteredCountryList(search) {
            if (!search || !search.trim()) return this.countries;
            const term = search.toLowerCase().trim();
            return this.countries.filter(item => item.name.toLowerCase().includes(term));
        },
        setCountry(id) {
            this.form.country_id = id;
            this.updateFilteredUniversities();
            if (this.form.university_id) {
                const stillExists = this.filteredUniversities.some(u => String(u.id) === String(this.form.university_id));
                if (!stillExists) {
                    this.form.university_id = '';
                }
            }
        },
        updateFilteredUniversities() {
            if (!this.form.country_id) {
                this.filteredUniversities = [];
            } else {
                this.filteredUniversities = this.universities.filter(u => String(u.country_id) === String(this.form.country_id));
            }
        },
        selectedUniversityName() {
            if (!this.form.university_id) return '';
            const u = this.universities.find(item => String(item.id) === String(this.form.university_id));
            return u ? u.name : '';
        },
        filteredUniversitiesBySearch(search) {
            let list = this.filteredUniversities;
            if (search && search.trim()) {
                const term = search.toLowerCase().trim();
                list = list.filter(item => item.name.toLowerCase().includes(term));
            }
            return list;
        },
        onInvestmentToggle() {
            if (this.form.is_investment) {
                if (tourCategory) {
                    this.form.chart_of_account_id = tourCategory.id;
                }
                if (!this.form.investment_purpose) {
                    this.form.investment_purpose = 'Partnership Negotiation';
                }
            } else {
                if (tourCategory && this.form.chart_of_account_id == tourCategory.id) {
                    this.form.chart_of_account_id = '';
                }
            }
        }
    };
}
</script>
@endpush

@endsection
