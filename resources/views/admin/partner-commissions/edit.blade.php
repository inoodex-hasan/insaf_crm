@extends('admin.layouts.master')

@section('title', 'Edit Partner Commission')

@section('content')

<div x-data="partnerCommissionEditHandler()">
    <!-- Header -->
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wider text-dark dark:text-white-light">Edit Partner Commission</h2>
            <p class="text-xs text-white-dark mt-1">Update agreement terms, rates, handover notes, or processing status.</p>
        </div>
        <div>
            <a href="{{ route('admin.partner-commissions.show', $partnerCommission->id) }}" class="btn btn-secondary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Details
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="mb-5 rounded-lg bg-danger/10 p-4 text-danger">
            <div class="font-bold mb-1 text-sm">Please fix the following errors:</div>
            <ul class="list-disc pl-5 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.partner-commissions.update', $partnerCommission->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Commission Channel & Partnership Type -->
        <div class="panel p-6 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#1b2e4b]">
            <h5 class="text-base font-bold text-dark dark:text-white-light mb-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-3">
                1. Partnership Channel & Cash Flow
            </h5>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Channel Selector -->
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark dark:text-white-light">
                        Partnership Channel <span class="text-danger">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-lg border cursor-pointer select-none transition"
                               :class="form.commission_type === 'university' ? 'border-primary bg-primary/5 text-primary font-bold' : 'border-[#e0e6ed] dark:border-[#1b2e4b] hover:bg-gray-50 dark:hover:bg-[#121e32]'">
                            <input type="radio" name="commission_type" value="university" x-model="form.commission_type" class="form-radio text-primary" />
                            <span>University Direct</span>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-lg border cursor-pointer select-none transition"
                               :class="form.commission_type === 'agency' ? 'border-primary bg-primary/5 text-primary font-bold' : 'border-[#e0e6ed] dark:border-[#1b2e4b] hover:bg-gray-50 dark:hover:bg-[#121e32]'">
                            <input type="radio" name="commission_type" value="agency" x-model="form.commission_type" class="form-radio text-primary" />
                            <span>Partner Agency</span>
                        </label>
                    </div>
                </div>

                <!-- Cash Direction -->
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark dark:text-white-light">
                        Cash Flow Direction <span class="text-danger">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-lg border cursor-pointer select-none transition"
                               :class="form.direction === 'receivable' ? 'border-success bg-success/5 text-success font-bold' : 'border-[#e0e6ed] dark:border-[#1b2e4b] hover:bg-gray-50 dark:hover:bg-[#121e32]'">
                            <input type="radio" name="direction" value="receivable" x-model="form.direction" class="form-radio text-success" />
                            <div>
                                <div class="text-sm">Receivable (Incoming)</div>
                                <div class="text-[11px] font-normal text-gray-500">Partner pays Insaf</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-lg border cursor-pointer select-none transition"
                               :class="form.direction === 'payable' ? 'border-danger bg-danger/5 text-danger font-bold' : 'border-[#e0e6ed] dark:border-[#1b2e4b] hover:bg-gray-50 dark:hover:bg-[#121e32]'">
                            <input type="radio" name="direction" value="payable" x-model="form.direction" class="form-radio text-danger" />
                            <div>
                                <div class="text-sm">Payable (Outgoing)</div>
                                <div class="text-[11px] font-normal text-gray-500">Insaf pays Partner</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Agency Specific: Referral Direction -->
            <div x-show="form.commission_type === 'agency'" x-transition class="mt-5 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                <label class="block text-sm font-semibold mb-2 text-dark dark:text-white-light">
                    Agency Collaboration Type <span class="text-danger">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3.5 rounded-lg border cursor-pointer select-none transition"
                           :class="form.agency_referral_type === 'outbound_handover' ? 'border-amber-500 bg-amber-50/50 text-amber-800 dark:text-amber-400 font-bold' : 'border-[#e0e6ed] dark:border-[#1b2e4b] hover:bg-gray-50 dark:hover:bg-[#121e32]'">
                        <input type="radio" name="agency_referral_type" value="outbound_handover" x-model="form.agency_referral_type" class="form-radio text-amber-600" />
                        <div>
                            <div class="text-sm">Outbound Handover</div>
                            <div class="text-[11px] font-normal text-gray-500">We take papers and hand over file to Partner Agency (e.g. China)</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-lg border cursor-pointer select-none transition"
                           :class="form.agency_referral_type === 'inbound_referral' ? 'border-cyan-500 bg-cyan-50/50 text-cyan-800 dark:text-cyan-400 font-bold' : 'border-[#e0e6ed] dark:border-[#1b2e4b] hover:bg-gray-50 dark:hover:bg-[#121e32]'">
                        <input type="radio" name="agency_referral_type" value="inbound_referral" x-model="form.agency_referral_type" class="form-radio text-cyan-600" />
                        <div>
                            <div class="text-sm">Inbound Sub-Agent</div>
                            <div class="text-[11px] font-normal text-gray-500">Outside sub-agent refers student file to Insaf</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- 2. Student Selection & File Information -->
        <div class="panel p-6 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#1b2e4b]">
            <h5 class="text-base font-bold text-dark dark:text-white-light mb-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-3">
                2. Student File & Case Association
            </h5>

            <!-- Student Search Field with Dropdown Searchbar -->
            <div class="form-group mb-5">
                <label class="block text-sm font-semibold mb-1.5 text-dark dark:text-white-light">
                    Select Student <span class="text-danger">*</span>
                </label>
                <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                    <input type="hidden" name="student_id" :value="form.student_id" required>
                    <button type="button" @click="open = !open"
                            class="form-input text-left flex items-center justify-between min-h-[42px]">
                        <div>
                            <template x-if="selectedStudent">
                                <div>
                                    <span class="font-bold text-dark dark:text-white-light" x-text="selectedStudent.name"></span>
                                    <span class="text-xs text-gray-500 ml-2" x-text="'Passport: ' + (selectedStudent.passport || 'None') + ' | ' + (selectedStudent.country_name || 'No Country')"></span>
                                </div>
                            </template>
                            <template x-if="!selectedStudent">
                                <span class="text-gray-400">Select Student</span>
                            </template>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak
                         class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg shadow-xl p-2 max-h-64 overflow-hidden flex flex-col">
                        <input type="text" x-model="search"
                               class="form-input text-xs mb-2 py-1.5 px-2.5">
                        <div class="overflow-y-auto max-h-52 space-y-1">
                            <template x-for="s in filteredStudentList(search)" :key="s.id">
                                <div @click="setStudent(s); open = false; search = ''"
                                     class="px-3 py-2 text-xs rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-[#121e32] flex items-center justify-between"
                                     :class="form.student_id == s.id ? 'bg-primary/10 text-primary font-bold' : ''">
                                    <div>
                                        <div class="font-bold text-dark dark:text-white-light" x-text="s.name"></div>
                                        <div class="text-[11px] text-gray-500" x-text="'Passport: ' + (s.passport || 'None') + ' | ' + (s.phone || 'No phone')"></div>
                                    </div>
                                    <span class="badge bg-primary/10 text-primary text-[10px]" x-text="s.country_name || 'No Country'"></span>
                                </div>
                            </template>
                            <div x-show="filteredStudentList(search).length === 0" class="text-xs text-white-dark p-3 text-center">
                                No students found
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Student Information Card -->
            <div x-show="selectedStudent" x-transition class="rounded-lg bg-gray-50 p-4 dark:bg-[#121e32] border border-[#e0e6ed] dark:border-[#1b2e4b] mb-5">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-base font-bold text-dark dark:text-white-light" x-text="selectedStudent?.name"></div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 mt-1">
                            <span>Passport: <strong class="text-dark dark:text-gray-300" x-text="selectedStudent?.passport || 'N/A'"></strong></span>
                            <span>Phone: <strong class="text-dark dark:text-gray-300" x-text="selectedStudent?.phone || 'N/A'"></strong></span>
                            <span>Country: <strong class="text-primary" x-text="selectedStudent?.country_name || 'N/A'"></strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partner Selection, Country, Officer & Ref (Balanced 2x2 Grid with Searchbars) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Col 1: Destination Country with Searchbar -->
                <div class="form-group">
                    <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                        Target Country
                    </label>
                    <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                        <input type="hidden" name="country_id" :value="form.country_id">
                        <button type="button" @click="open = !open"
                                class="form-input text-left flex items-center justify-between">
                            <span x-text="selectedCountryName() || 'Select Country'"></span>
                            <svg class="w-4 h-4 text-gray-500 shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                             class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg shadow-xl p-2 max-h-60 overflow-hidden flex flex-col">
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

                <!-- Col 2: Partner Selection with Searchbar (University or Partner Agency) -->
                <div class="form-group">
                    <!-- When University Direct with Searchbar (filtered by country) -->
                    <div x-show="form.commission_type === 'university'">
                        <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                            University <span class="text-danger">*</span>
                        </label>
                        <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                            <input type="hidden" name="university_id" :value="form.university_id" :required="form.commission_type === 'university'">
                            <button type="button" @click="open = !open"
                                    class="form-input text-left flex items-center justify-between">
                                <span x-text="selectedUniversityName() || (form.country_id ? 'Select University' : 'Select University (or filter by Country)')"></span>
                                <svg class="w-4 h-4 text-gray-500 shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg shadow-xl p-2 max-h-60 overflow-hidden flex flex-col">
                                <input type="text" x-model="search"
                                       class="form-input text-xs mb-2 py-1.5 px-2.5">
                                <div class="overflow-y-auto max-h-48 space-y-1">
                                    <div @click="setUniversity(''); open = false; search = ''"
                                         class="px-3 py-1.5 text-xs rounded cursor-pointer text-white-dark hover:bg-gray-100 dark:hover:bg-[#121e32]">
                                        None / Clear
                                    </div>
                                    <template x-for="uni in filteredUniversityList(search)" :key="uni.id">
                                        <div @click="setUniversity(uni.id); open = false; search = ''"
                                             class="px-3 py-1.5 text-xs rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-[#121e32]"
                                             :class="form.university_id == uni.id ? 'bg-primary/10 text-primary font-bold' : ''"
                                             x-text="uni.name">
                                        </div>
                                    </template>
                                    <div x-show="filteredUniversityList(search).length === 0" class="text-xs text-white-dark p-2 text-center">
                                        No universities found
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- When Partner Agency with Searchbar -->
                    <div x-show="form.commission_type === 'agency'">
                        <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                            Partner Agency <span class="text-danger">*</span>
                        </label>
                        <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                            <input type="hidden" name="partner_agency_id" :value="form.partner_agency_id" :required="form.commission_type === 'agency'">
                            <button type="button" @click="open = !open"
                                    class="form-input text-left flex items-center justify-between">
                                <span x-text="selectedAgencyName() || 'Select Partner Agency'"></span>
                                <svg class="w-4 h-4 text-gray-500 shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg shadow-xl p-2 max-h-60 overflow-hidden flex flex-col">
                                <input type="text" x-model="search"
                                       class="form-input text-xs mb-2 py-1.5 px-2.5">
                                <div class="overflow-y-auto max-h-48 space-y-1">
                                    <div @click="setAgency(''); open = false; search = ''"
                                         class="px-3 py-1.5 text-xs rounded cursor-pointer text-white-dark hover:bg-gray-100 dark:hover:bg-[#121e32]">
                                        None / Clear
                                    </div>
                                    <template x-for="agency in filteredAgencyList(search)" :key="agency.id">
                                        <div @click="setAgency(agency.id); open = false; search = ''"
                                             class="px-3 py-1.5 text-xs rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-[#121e32] flex items-center justify-between"
                                             :class="form.partner_agency_id == agency.id ? 'bg-primary/10 text-primary font-bold' : ''">
                                            <span x-text="agency.name"></span>
                                            <span x-show="agency.contact_person" class="text-[11px] text-gray-400" x-text="agency.contact_person"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredAgencyList(search).length === 0" class="text-xs text-white-dark p-2 text-center">
                                        No partner agencies found
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col 3: Handover Officer / Staff in Charge with Searchbar -->
                <div class="form-group">
                    <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                        Handover / Assigned Officer
                    </label>
                    <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                        <input type="hidden" name="handover_officer_id" :value="form.handover_officer_id">
                        <button type="button" @click="open = !open"
                                class="form-input text-left flex items-center justify-between">
                            <span x-text="selectedOfficerName() || 'Select Officer'"></span>
                            <svg class="w-4 h-4 text-gray-500 shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                             class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg shadow-xl p-2 max-h-60 overflow-hidden flex flex-col">
                            <input type="text" x-model="search"
                                   class="form-input text-xs mb-2 py-1.5 px-2.5">
                            <div class="overflow-y-auto max-h-48 space-y-1">
                                <div @click="setOfficer(''); open = false; search = ''"
                                     class="px-3 py-1.5 text-xs rounded cursor-pointer text-white-dark hover:bg-gray-100 dark:hover:bg-[#121e32]">
                                    None / Unassigned
                                </div>
                                <template x-for="user in filteredOfficerList(search)" :key="user.id">
                                    <div @click="setOfficer(user.id); open = false; search = ''"
                                         class="px-3 py-1.5 text-xs rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-[#121e32]"
                                         :class="form.handover_officer_id == user.id ? 'bg-primary/10 text-primary font-bold' : ''"
                                         x-text="user.name">
                                    </div>
                                </template>
                                <div x-show="filteredOfficerList(search).length === 0" class="text-xs text-white-dark p-2 text-center">
                                    No officers found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col 4: Agreement Reference No -->
                <div>
                    <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                        Agreement / Contract Ref No
                    </label>
                    <input type="text" name="agreement_reference" value="{{ old('agreement_reference', $partnerCommission->agreement_reference) }}" class="form-input" />
                </div>
            </div>
        </div>

        <!-- 3. Financial Calculation & Rates -->
        <div class="panel p-6 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#1b2e4b]">
            <h5 class="text-base font-bold text-dark dark:text-white-light mb-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-3">
                3. Commission Terms & Financial Calculation
            </h5>

            <!-- Row 1: Currency & Calculation Mode (Balanced 2 columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                        Currency <span class="text-danger">*</span>
                    </label>
                    <select name="currency" x-model="form.currency" required class="form-select font-bold">
                        <option value="BDT">BDT (৳)</option>
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="GBP">GBP (£)</option>
                        <option value="CNY">CNY / RMB (¥)</option>
                        <option value="MYR">MYR (RM)</option>
                        <option value="RUB">RUB (₽)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                        Calculation Mode <span class="text-danger">*</span>
                    </label>
                    <select name="rate_type" x-model="form.rate_type" required class="form-select">
                        <option value="fixed">Fixed Amount</option>
                        <option value="percentage">Percentage (%)</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: Rates & Exchange Rate (Balanced Grid) -->
            <div class="mb-5">
                <!-- When Percentage Mode -->
                <div x-show="form.rate_type === 'percentage'" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                            Base Amount (Tuition Fee) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="base_amount" x-model.number="form.base_amount" class="form-input" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                            Commission Rate (%) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="rate_value" x-model.number="form.rate_value" required class="form-input font-bold" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                            Exchange Rate (to BDT) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.0001" min="0.0001" name="exchange_rate" x-model.number="form.exchange_rate" required class="form-input font-bold" />
                    </div>
                </div>

                <!-- When Fixed Amount Mode -->
                <div x-show="form.rate_type === 'fixed'" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                            Fixed Amount <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="rate_value" x-model.number="form.rate_value" required class="form-input font-bold" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                            Exchange Rate (to BDT) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.0001" min="0.0001" name="exchange_rate" x-model.number="form.exchange_rate" required class="form-input font-bold" />
                    </div>
                </div>
            </div>

            <!-- Calculation Output Highlight Box -->
            <div class="rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 p-5 dark:from-[#121e32] dark:to-[#1b2e4b] border border-blue-200 dark:border-blue-900/50">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    <div>
                        <div class="text-xs uppercase text-gray-500 font-semibold">Agreed Commission (Foreign)</div>
                        <div class="text-xl font-bold text-dark dark:text-white-light mt-1">
                            <span x-text="form.currency"></span>
                            <span x-text="calculatedCommission().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs uppercase text-gray-500 font-semibold">Exchange Rate</div>
                        <div class="text-base font-bold text-gray-700 dark:text-gray-300 mt-1">
                            1 <span x-text="form.currency"></span> = <span x-text="form.exchange_rate"></span> BDT
                        </div>
                    </div>
                    <div>
                        <div class="text-xs uppercase text-primary font-semibold">Total Settlement Value (BDT)</div>
                        <div class="text-2xl font-extrabold text-primary mt-1">
                            ৳<span x-text="calculatedTotalBdt().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. File Processing Journey & Stages -->
        <div class="panel p-6 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#1b2e4b]">
            <h5 class="text-base font-bold text-dark dark:text-white-light mb-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-3">
                4. File Processing Journey & Milestone
            </h5>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <!-- Handover Date -->
                <div>
                    <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                        Handover / Agreement Date
                    </label>
                    <input type="date" name="handover_date" value="{{ old('handover_date', optional($partnerCommission->handover_date)->format('Y-m-d')) }}" class="form-input" />
                </div>

                <!-- Processing Stage -->
                <div>
                    <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                        Current Processing Stage
                    </label>
                    <select name="processing_stage" class="form-select">
                        <option value="File Handed Over to Agency" {{ $partnerCommission->processing_stage === 'File Handed Over to Agency' ? 'selected' : '' }}>File Handed Over to Agency</option>
                        <option value="Documents Verification" {{ $partnerCommission->processing_stage === 'Documents Verification' ? 'selected' : '' }}>Documents Verification</option>
                        <option value="Applied / Awaiting Offer Letter" {{ $partnerCommission->processing_stage === 'Applied / Awaiting Offer Letter' ? 'selected' : '' }}>Applied / Awaiting Offer Letter</option>
                        <option value="Offer Letter Received" {{ $partnerCommission->processing_stage === 'Offer Letter Received' ? 'selected' : '' }}>Offer Letter Received</option>
                        <option value="Tuition Fee Paid" {{ $partnerCommission->processing_stage === 'Tuition Fee Paid' ? 'selected' : '' }}>Tuition Fee Paid</option>
                        <option value="Visa Application Submitted" {{ $partnerCommission->processing_stage === 'Visa Application Submitted' ? 'selected' : '' }}>Visa Application Submitted</option>
                        <option value="Visa Issued / Enrolled" {{ $partnerCommission->processing_stage === 'Visa Issued / Enrolled' ? 'selected' : '' }}>Visa Issued / Enrolled</option>
                        <option value="Completed / Commission Due" {{ $partnerCommission->processing_stage === 'Completed / Commission Due' ? 'selected' : '' }}>Completed / Commission Due</option>
                    </select>
                </div>

                <!-- Workflow Status -->
                <div>
                    <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                        File Status <span class="text-danger">*</span>
                    </label>
                    <select name="workflow_status" required class="form-select font-bold">
                        <option value="pending" {{ $partnerCommission->workflow_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $partnerCommission->workflow_status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="visa_approved" {{ $partnerCommission->workflow_status === 'visa_approved' ? 'selected' : '' }}>Visa Approved</option>
                        <option value="settled" {{ $partnerCommission->workflow_status === 'settled' ? 'selected' : '' }}>Settled</option>
                        <option value="cancelled" {{ $partnerCommission->workflow_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Processing Notes & Timeline Details -->
            <div>
                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                    Processing Notes & Handover Terms
                </label>
                <textarea name="processing_notes" rows="3" class="form-textarea">{{ old('processing_notes', $partnerCommission->processing_notes) }}</textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end items-center gap-3">
            <a href="{{ route('admin.partner-commissions.show', $partnerCommission->id) }}" class="btn btn-outline-danger">Cancel</a>
            <button type="submit" class="btn btn-primary px-8">Update Commission Record</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function partnerCommissionEditHandler() {
    const students = @js($students);
    const initialStudentId = '{{ $partnerCommission->student_id }}';
    const initialStudent = students.find(s => String(s.id) === String(initialStudentId)) || null;

    return {
        students: students,
        countries: @js($countries),
        universities: @js($universities),
        agencies: @js($agencies),
        users: @js($users),
        selectedStudent: initialStudent,
        form: {
            student_id: initialStudentId,
            commission_type: '{{ $partnerCommission->commission_type }}',
            direction: '{{ $partnerCommission->direction }}',
            agency_referral_type: '{{ $partnerCommission->agency_referral_type ?: 'outbound_handover' }}',
            university_id: '{{ $partnerCommission->university_id }}',
            partner_agency_id: '{{ $partnerCommission->partner_agency_id }}',
            country_id: '{{ $partnerCommission->country_id }}',
            handover_officer_id: '{{ $partnerCommission->handover_officer_id }}',
            currency: '{{ $partnerCommission->currency }}',
            rate_type: '{{ $partnerCommission->rate_type }}',
            rate_value: {{ $partnerCommission->rate_value }},
            base_amount: {{ $partnerCommission->base_amount ?: 0 }},
            exchange_rate: {{ $partnerCommission->exchange_rate }},
        },
        // Student helpers
        filteredStudentList(search) {
            if (!search) return this.students.slice(0, 15);
            const q = search.toLowerCase();
            return this.students.filter(s =>
                (s.name && s.name.toLowerCase().includes(q)) ||
                (s.passport && s.passport.toLowerCase().includes(q)) ||
                (s.phone && s.phone.includes(q))
            ).slice(0, 15);
        },
        setStudent(s) {
            this.selectedStudent = s;
            this.form.student_id = s.id;
            if (s.country_id && !this.form.country_id) {
                this.form.country_id = s.country_id;
            }
            if (s.university_id && !this.form.university_id) {
                this.form.university_id = s.university_id;
            }
        },
        // Country helpers
        selectedCountryName() {
            if (!this.form.country_id) return '';
            const c = this.countries.find(item => String(item.id) === String(this.form.country_id));
            return c ? c.name : '';
        },
        filteredCountryList(search) {
            if (!search) return this.countries;
            const q = search.toLowerCase();
            return this.countries.filter(c => c.name && c.name.toLowerCase().includes(q));
        },
        setCountry(id) {
            this.form.country_id = id;
            if (this.form.university_id) {
                const uni = this.universities.find(u => String(u.id) === String(this.form.university_id));
                if (uni && id && String(uni.country_id) !== String(id)) {
                    this.form.university_id = '';
                }
            }
        },
        // University helpers (filtered strictly by country if selected)
        selectedUniversityName() {
            if (!this.form.university_id) return '';
            const u = this.universities.find(item => String(item.id) === String(this.form.university_id));
            return u ? u.name : '';
        },
        filteredUniversityList(search) {
            let list = this.universities;
            if (this.form.country_id) {
                list = list.filter(u => String(u.country_id) === String(this.form.country_id));
            }
            if (!search) return list;
            const q = search.toLowerCase();
            return list.filter(u => u.name && u.name.toLowerCase().includes(q));
        },
        setUniversity(id) {
            this.form.university_id = id;
            if (id && !this.form.country_id) {
                const uni = this.universities.find(u => String(u.id) === String(id));
                if (uni && uni.country_id) {
                    this.form.country_id = uni.country_id;
                }
            }
        },
        // Agency helpers
        selectedAgencyName() {
            if (!this.form.partner_agency_id) return '';
            const a = this.agencies.find(item => String(item.id) === String(this.form.partner_agency_id));
            return a ? a.name : '';
        },
        filteredAgencyList(search) {
            if (!search) return this.agencies;
            const q = search.toLowerCase();
            return this.agencies.filter(a =>
                (a.name && a.name.toLowerCase().includes(q)) ||
                (a.contact_person && a.contact_person.toLowerCase().includes(q))
            );
        },
        setAgency(id) {
            this.form.partner_agency_id = id;
        },
        // Officer helpers
        selectedOfficerName() {
            if (!this.form.handover_officer_id) return '';
            const u = this.users.find(item => String(item.id) === String(this.form.handover_officer_id));
            return u ? u.name : '';
        },
        filteredOfficerList(search) {
            if (!search) return this.users;
            const q = search.toLowerCase();
            return this.users.filter(u => u.name && u.name.toLowerCase().includes(q));
        },
        setOfficer(id) {
            this.form.handover_officer_id = id;
        },
        calculatedCommission() {
            if (this.form.rate_type === 'percentage') {
                return ((Number(this.form.base_amount) || 0) * (Number(this.form.rate_value) || 0)) / 100;
            }
            return Number(this.form.rate_value) || 0;
        },
        calculatedTotalBdt() {
            return this.calculatedCommission() * (Number(this.form.exchange_rate) || 1);
        }
    };
}
</script>
@endpush

@endsection
