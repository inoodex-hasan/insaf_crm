@extends('admin.layouts.master')

@section('title', 'Learning Resources & Templates')

@section('content')
<div x-data="{
    activeTab: '{{ $activeTab ?? 'agreements' }}',
    agreementFilterCountry: 'all',
    agreementSearch: '',
    editModalOpen: false,
    editTemplateId: null,
    editTemplateTitle: '',
    editTemplateHeaderSpace: 0,
    editTemplateContent: '',
    editTemplateLayout: 'pad',
    editTemplateActive: true,
    
    docModalOpen: false,
    interviewModalOpen: false,

    openEditModal(template) {
        this.editTemplateId = template.id;
        this.editTemplateTitle = template.title;
        this.editTemplateHeaderSpace = template.header_space_mm || 0;
        this.editTemplateContent = template.content;
        this.editTemplateLayout = template.layout_type;
        this.editTemplateActive = !!template.is_active;
        this.editModalOpen = true;
    }
}" class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h4 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">LEARNING RESOURCES & TEMPLATES</h4>
            <p class="text-xs text-white-dark mt-1">Official agency contract templates, clearance certificates, country-based documentation, and interview preparation materials.</p>
        </div>

        <div class="flex items-center gap-2">
            <template x-if="activeTab === 'documents'">
                <button type="button" @click="docModalOpen = true" class="btn btn-primary flex items-center gap-2 text-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Upload Country Document
                </button>
            </template>
            <template x-if="activeTab === 'interview'">
                <button type="button" @click="interviewModalOpen = true" class="btn btn-primary flex items-center gap-2 text-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Interview Resource
                </button>
            </template>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="flex items-center rounded bg-success-light p-3.5 text-success dark:bg-success-dark-light">
            <span class="ltr:pr-2 rtl:pl-2"><strong class="ltr:mr-1 rtl:ml-1">Success!</strong> {{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded bg-danger-light p-3.5 text-danger dark:bg-danger-dark-light">
            <ul class="list-disc pl-5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Navigation Tabs -->
    <div class="border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <!-- Tab 1: Agreements -->
            <li class="mr-2">
                <button type="button" 
                        @click="activeTab = 'agreements'" 
                        :class="activeTab === 'agreements' ? 'text-primary border-primary active border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300'"
                        class="inline-flex items-center gap-2 p-4 rounded-t-lg transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Agreement Formats (Templates)
                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $agreementTemplates->count() }}
                    </span>
                </button>
            </li>

            <!-- Tab 2: Clearance Formats -->
            <li class="mr-2">
                <button type="button" 
                        @click="activeTab = 'clearances'" 
                        :class="activeTab === 'clearances' ? 'text-primary border-primary active border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300'"
                        class="inline-flex items-center gap-2 p-4 rounded-t-lg transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Clearance Formats (Templates)
                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                        {{ $clearanceTemplates->count() }}
                    </span>
                </button>
            </li>

            <!-- Tab 3: Country Documents -->
            <li class="mr-2">
                <button type="button" 
                        @click="activeTab = 'documents'" 
                        :class="activeTab === 'documents' ? 'text-primary border-primary active border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300'"
                        class="inline-flex items-center gap-2 p-4 rounded-t-lg transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    Country Documents (Uploads)
                    <span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        {{ $countryDocuments->count() }}
                    </span>
                </button>
            </li>

            <!-- Tab 4: Interview Preparation -->
            <li class="mr-2">
                <button type="button" 
                        @click="activeTab = 'interview'" 
                        :class="activeTab === 'interview' ? 'text-primary border-primary active border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300'"
                        class="inline-flex items-center gap-2 p-4 rounded-t-lg transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Interview Preparation
                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-800 dark:bg-rose-900 dark:text-rose-200">
                        {{ $interviewPreps->count() }}
                    </span>
                </button>
            </li>
        </ul>
    </div>

    <!-- TAB 1: AGREEMENT FORMATS (ORGANIZED BY DESTINATION COUNTRY) -->
    <div x-show="activeTab === 'agreements'" class="space-y-6">
        @php
            $groupedAgreements = $agreementTemplates->groupBy(function($item) {
                return $item->country->name ?? 'General / Other Countries';
            });
        @endphp

        <!-- Top Overview & Architecture Card -->
        <div class="rounded-xl border border-[#e0e6ed] bg-gradient-to-r from-blue-50/50 via-white to-slate-50 p-5 shadow-sm dark:border-[#1b2e4b] dark:from-[#1b2e4b]/40 dark:via-[#1b2e4b]/20 dark:to-[#1b2e4b]/40">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="space-y-1">
                    <h5 class="text-sm font-bold text-dark dark:text-white-light flex items-center gap-2">
                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Official Service Agreements & Contract Formats
                    </h5>
                    <p class="text-xs text-white-dark leading-relaxed">
                        Master agreement templates organized by destination country. Each country offers both <strong>Agency Official Pad</strong> (with embedded watermark logo &amp; header) and <strong>Non-Judicial Stamp Paper</strong> (with 90mm top clearance for physical revenue stamp papers).
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                        {{ $groupedAgreements->count() }} Country Portfolios
                    </span>
                    <span class="rounded-lg bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                        {{ $agreementTemplates->count() }} Active Formats
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#e0e6ed] bg-white p-3.5 shadow-sm dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
            <!-- Country Pills -->
            <div class="flex flex-wrap items-center gap-1.5">
                <button type="button" 
                        @click="agreementFilterCountry = 'all'"
                        :class="agreementFilterCountry === 'all' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700'"
                        class="rounded-lg px-3 py-1.5 text-xs transition flex items-center gap-1.5">
                    <span> All Countries</span>
                    <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="agreementFilterCountry === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200'">{{ $agreementTemplates->count() }}</span>
                </button>

                @foreach($groupedAgreements as $cName => $cItems)
                    @php 
                        $cSlug = Str::slug($cName);
                        $cFlag = '';
                        if(str_contains(strtolower($cName), 'malta')) $cFlag = '';
                        elseif(str_contains(strtolower($cName), 'korea')) $cFlag = '';
                        elseif(str_contains(strtolower($cName), 'malaysia')) $cFlag = '';
                        elseif(str_contains(strtolower($cName), 'russia')) $cFlag = '';
                    @endphp
                    <button type="button" 
                            @click="agreementFilterCountry = '{{ $cSlug }}'"
                            :class="agreementFilterCountry === '{{ $cSlug }}' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700'"
                            class="rounded-lg px-3 py-1.5 text-xs transition flex items-center gap-1.5">
                        <span>{{ $cFlag }} {{ $cName }}</span>
                        <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="agreementFilterCountry === '{{ $cSlug }}' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200'">{{ $cItems->count() }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Instant Search Input -->
            <div class="relative w-full sm:w-64">
                <input type="text" 
                       x-model="agreementSearch" 
                       placeholder="Filter by title or clause..." 
                       class="form-input text-xs pl-8 pr-3 py-1.5 w-full">
                <svg class="h-4 w-4 text-white-dark absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- Country-Grouped Agreement Sections -->
        <div class="space-y-6">
            @foreach($groupedAgreements as $countryName => $items)
                @php
                    $sortedItems = $items->sortBy(fn($item) => $item->layout_type === 'pad' ? 0 : 1);
                    $cSlug = Str::slug($countryName);
                    
                    $flag = '🌐';
                    $feeBadge = null;
                    $clausesBadge = null;
                    $summaryDesc = 'Standard bilateral agreement clauses including student particulars, milestone payments, genuine document certification, and mutual discharge.';

                @endphp

                <div class="rounded-xl border border-[#e0e6ed] bg-white shadow-sm dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden"
                     x-show="(agreementFilterCountry === 'all' || agreementFilterCountry === '{{ $cSlug }}') && (!agreementSearch || '{{ strtolower($countryName . ' ' . $sortedItems->pluck('title')->implode(' ')) }}'.includes(agreementSearch.toLowerCase()))">
                    
                    <!-- Country Header Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <div class="flex items-center gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h5 class="text-base font-bold text-dark dark:text-white-light tracking-wide">
                                        {{ strtoupper($countryName) }} STUDY PERMIT AGREEMENT
                                    </h5>
                                    <span class="rounded bg-primary/10 px-2 py-0.5 text-[11px] font-bold text-primary">
                                        {{ $sortedItems->count() }} {{ Str::plural('Format', $sortedItems->count()) }}
                                    </span>
                                </div>
                                <p class="text-xs text-white-dark mt-0.5">{{ $summaryDesc }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Layout Cards Comparison Grid -->
                    <div class="p-6">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
                            @foreach($sortedItems as $template)
                                @php
                                    $isPad = $template->layout_type === 'pad';
                                @endphp

                                <div class="rounded-xl border transition hover:shadow-md p-5 flex flex-col justify-between {{ $isPad ? 'border-blue-200 bg-blue-50/15 dark:border-blue-900/40 dark:bg-[#121e32]/30' : 'border-amber-200 bg-amber-50/15 dark:border-amber-900/40 dark:bg-[#121e32]/30' }}">
                                    
                                    <!-- Card Header -->
                                    <div>
                                        <div class="flex items-start justify-between gap-3 mb-3">
                                            @if($isPad)
                                                <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-800 border border-blue-200 dark:bg-blue-900/50 dark:text-blue-300">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    Agency Official Pad Layout
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800 border border-amber-300 dark:bg-amber-900/50 dark:text-amber-300">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Stamp Paper Layout ({{ $template->header_space_mm }}mm Clearance)
                                                </span>
                                            @endif

                                            <button type="button" 
                                                    @click="openEditModal({{ json_encode($template) }})" 
                                                    class="text-slate-400 hover:text-primary transition p-1" 
                                                    title="Edit Template Settings & Content">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <h6 class="text-sm font-bold text-dark dark:text-white-light leading-snug mb-2">
                                            {{ $template->title }}
                                        </h6>

                                        <!-- Format Highlights -->
                                        <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400 mt-2 mb-4">
                                            @if($isPad)
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Includes official Insaf background watermark logo</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Official pad headers &amp; CEO signatory block</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Standard A4 orientation for agency &amp; client files</span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="h-3.5 w-3.5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span><strong>{{ $template->header_space_mm }}mm Top Space</strong> for physical revenue stamp</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="h-3.5 w-3.5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Blank header margin (no watermark collision with seal)</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="h-3.5 w-3.5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Direct printer feed for 100/300 BDT non-judicial paper</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Action Buttons Bar -->
                                    <div class="pt-3 border-t border-[#e0e6ed] dark:border-[#1b2e4b] flex flex-wrap items-center justify-between gap-2 mt-auto">
                                        <div class="flex items-center gap-1.5">

                                            <!-- Word (.docx) -->
                                            <a href="{{ route('admin.learning-resources.templates.download-docx', $template) }}" 
                                               class="btn btn-primary btn-sm px-2.5 py-1 text-xs flex items-center gap-1" 
                                               style="background-color: #2563eb; color: white;"
                                               title="Download Editable Word (.docx) Document">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Word (.docx)
                                            </a>
                                        </div>

                                        <!-- Edit Template Button -->
                                        <button type="button" 
                                                @click="openEditModal({{ json_encode($template) }})" 
                                                class="btn btn-outline-dark btn-sm px-2.5 py-1 text-xs flex items-center gap-1"
                                                title="Edit Template Content & Settings">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Edit
                                        </button>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 2: CLEARANCE FORMATS -->
    <div x-show="activeTab === 'clearances'" class="space-y-6">
        <div class="rounded-lg border border-[#e0e6ed] bg-[#f8fafc] p-4 text-xs text-slate-600 dark:border-[#1b2e4b] dark:bg-[#1b2e4b]/40 dark:text-slate-300">
            <strong>Clearance Formats:</strong> Official certificates for student file discharge. Includes:
            (1) <em>Withdrawal File Clearance</em> for cancellation & document/fee returns, and 
            (2) <em>Full Clearance (All Done)</em> upon successful visa approval, pre-departure briefing, and completed flight arrangements.
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 1.5rem;">
            @foreach($clearanceTemplates as $template)
            <div class="rounded-xl border border-[#e0e6ed] bg-white p-5 shadow-sm transition hover:shadow-md dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            @if($template->type === 'clearance_withdrawal')
                                <span class="rounded bg-rose-100 px-2 py-0.5 text-xs font-bold text-rose-800 border border-rose-300 dark:bg-rose-900/40 dark:text-rose-300">
                                    Withdrawal & Document Return
                                </span>
                            @else
                                <span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-800 border border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Full Clearance (All Done)
                                </span>
                            @endif

                            <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                Official Pad Format
                            </span>
                        </div>
                        <h5 class="text-base font-bold text-dark dark:text-white-light leading-snug">
                            {{ $template->title }}
                        </h5>
                    </div>

                    <button type="button" 
                            @click="openEditModal({{ json_encode($template) }})" 
                            class="text-slate-400 hover:text-primary transition p-1" 
                            title="Edit Template">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                </div>

                <p class="mt-3 text-xs text-white-dark line-clamp-2">
                    @if($template->type === 'clearance_withdrawal')
                        Official declaration acknowledging return of all physical passports, academic certificates, financial settlement calculation, and complete mutual discharge.
                    @else
                        Official completion certificate certifying visa sticker/eVisa handover, flight briefing confirmation, zero outstanding balance, and final sign-off.
                    @endif
                </p>

                <!-- Action Buttons -->
                <div class="mt-5 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b] flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.learning-resources.templates.preview', $template) }}" 
                       target="_blank" 
                       class="btn btn-outline-primary btn-sm flex items-center gap-1.5 text-xs px-2.5 py-1.5">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview / Print
                    </a>

                    <a href="{{ route('admin.learning-resources.templates.download-pdf', $template) }}" 
                       class="btn btn-danger btn-sm flex items-center gap-1.5 text-xs px-2.5 py-1.5" style="background-color: #e11d48; color: white;">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        PDF
                    </a>

                    <a href="{{ route('admin.learning-resources.templates.download-docx', $template) }}" 
                       class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs px-2.5 py-1.5" style="background-color: #2563eb; color: white;">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        DOCX
                    </a>

                    <button type="button" 
                            @click="openEditModal({{ json_encode($template) }})" 
                            class="btn btn-outline-dark btn-sm flex items-center gap-1.5 text-xs px-2.5 py-1.5 ml-auto">
                        Edit
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 3: COUNTRY BASED DOCUMENTS -->
    <div x-show="activeTab === 'documents'" class="space-y-6">
        <!-- Filter Header Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-[#e0e6ed] bg-white p-4 dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold uppercase text-slate-500">Filter By Country:</span>
                <a href="{{ route('admin.learning-resources.index', ['tab' => 'documents']) }}" 
                   class="rounded-full px-3 py-1 text-xs font-semibold transition {{ empty($countryFilter) ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}">
                    All Countries
                </a>
                @foreach($countries as $c)
                <a href="{{ route('admin.learning-resources.index', ['tab' => 'documents', 'country_id' => $c->id]) }}" 
                   class="rounded-full px-3 py-1 text-xs font-semibold transition {{ $countryFilter == $c->id ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}">
                    {{ $c->name }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Documents Table / Grid -->
        @if($countryDocuments->isEmpty())
            <div class="rounded-xl border border-dashed border-[#e0e6ed] p-12 text-center dark:border-[#1b2e4b] p-2">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <h5 class="mt-4 text-base font-bold text-dark dark:text-white-light">No documents uploaded yet</h5>
                <p class="mt-1 text-xs text-white-dark">Upload destination-specific checklists, guideline PDFs, sample forms, or attestation manuals.</p>
                <button type="button" @click="docModalOpen = true" class="btn btn-primary btn-sm mt-4 inline-flex items-center gap-1 text-xs">
                    Upload First Document
                </button>
            </div>
        @else
            <div class="table-responsive rounded-xl border border-[#e0e6ed] bg-white shadow-sm dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
                <table class="table-hover w-full text-left text-xs">
                    <thead class="bg-[#f8fafc] text-slate-600 dark:bg-[#121e32] dark:text-slate-300">
                        <tr>
                            <th class="py-3 px-4">Document Title</th>
                            <th class="py-3 px-4">Country</th>
                            <th class="py-3 px-4">File Type</th>
                            <th class="py-3 px-4">Size</th>
                            <th class="py-3 px-4">Uploaded By</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
                        @foreach($countryDocuments as $doc)
                        <tr>
                            <td class="py-3 px-4">
                                <div class="font-bold text-dark dark:text-white-light">{{ $doc->title }}</div>
                                @if($doc->description)
                                    <div class="text-[11px] text-white-dark mt-0.5 line-clamp-1">{{ $doc->description }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($doc->country)
                                    <span class="rounded bg-primary/10 px-2 py-0.5 font-bold text-primary">
                                        {{ $doc->country->name }}
                                    </span>
                                @else
                                    <span class="text-white-dark">General / All</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="rounded bg-slate-100 px-2 py-0.5 uppercase font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $doc->file_type ?? 'FILE' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-white-dark">
                                {{ $doc->formatted_file_size }}
                            </td>
                            <td class="py-3 px-4 text-white-dark">
                                {{ $doc->creator?->name ?? 'System' }}
                            </td>
                            <td class="py-3 px-4 text-white-dark">
                                {{ $doc->created_at->format('d M, Y') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($doc->file_path)
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" 
                                       target="_blank" 
                                       download 
                                       class="btn btn-outline-primary btn-sm px-2.5 py-1 text-xs flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </a>
                                    @endif

                                    <form action="{{ route('admin.learning-resources.documents.destroy', $doc) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this document?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger hover:text-danger-dark p-1" title="Delete Document">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- TAB 4: INTERVIEW PREPARATION -->
    <div x-show="activeTab === 'interview'" class="space-y-6">
        <!-- Filter Header Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-[#e0e6ed] bg-white p-4 dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold uppercase text-slate-500">Filter By Country:</span>
                <a href="{{ route('admin.learning-resources.index', ['tab' => 'interview']) }}" 
                   class="rounded-full px-3 py-1 text-xs font-semibold transition {{ empty($countryFilter) ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}">
                    All Countries
                </a>
                @foreach($countries as $c)
                <a href="{{ route('admin.learning-resources.index', ['tab' => 'interview', 'country_id' => $c->id]) }}" 
                   class="rounded-full px-3 py-1 text-xs font-semibold transition {{ $countryFilter == $c->id ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}">
                    {{ $c->name }}
                </a>
                @endforeach
            </div>
        </div>

        @if($interviewPreps->isEmpty())
            <div class="rounded-xl border border-dashed border-[#e0e6ed] p-12 text-center dark:border-[#1b2e4b] p-2">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <h5 class="mt-4 text-base font-bold text-dark dark:text-white-light">No interview prep materials added yet</h5>
                <p class="mt-1 text-xs text-white-dark">Add private YouTube videos, Google Drive guides, university mock interview tips, and common visa questions.</p>
                <button type="button" @click="interviewModalOpen = true" class="btn btn-primary btn-sm mt-4 inline-flex items-center gap-1 text-xs">
                    Add First Resource
                </button>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
                @foreach($interviewPreps as $item)
                <div class="rounded-xl border border-[#e0e6ed] bg-white p-5 shadow-sm transition hover:shadow-md dark:border-[#1b2e4b] dark:bg-[#1b2e4b] flex flex-col justify-between">
                    <div>
                        <!-- Header Badges -->
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="rounded bg-rose-100 px-2 py-0.5 text-xs font-bold text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                {{ $item->country?->name ?? 'General Interview' }}
                            </span>
                            @if($item->embed_url)
                                <span class="rounded bg-red-100 px-2 py-0.5 text-[11px] font-bold text-red-700 flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                    Video Included
                                </span>
                            @endif
                        </div>

                        <h5 class="text-base font-bold text-dark dark:text-white-light leading-snug">
                            {{ $item->title }}
                        </h5>

                        @if($item->description)
                            <p class="mt-2 text-xs text-white-dark line-clamp-3">
                                {{ $item->description }}
                            </p>
                        @endif

                        <!-- Embedded Video Preview Container (If YouTube / Google Drive) -->
                        @if($item->embed_url)
                            <div class="mt-3 overflow-hidden rounded-lg border border-[#e0e6ed] bg-black dark:border-[#1b2e4b]" style="aspect-ratio: 16/9;">
                                <iframe src="{{ $item->embed_url }}" 
                                        class="h-full w-full" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Links & Actions -->
                    <div class="mt-4 pt-3 border-t border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            @if($item->file_path)
                                <a href="{{ asset('storage/' . $item->file_path) }}" 
                                   target="_blank" 
                                   download 
                                   class="btn btn-outline-primary btn-sm px-2 py-1 text-xs flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Guide ({{ $item->file_type }})
                                </a>
                            @endif

                            @if($item->external_link)
                                <a href="{{ $item->external_link }}" 
                                   target="_blank" 
                                   class="text-primary hover:underline flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Drive / Cloud Link
                                </a>
                            @endif
                        </div>

                        <form action="{{ route('admin.learning-resources.interview-prep.destroy', $item) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to remove this interview preparation item?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-danger hover:text-danger-dark p-1" title="Delete">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: EDIT TEMPLATE (FULL VIEWPORT)     -->
    <!-- ========================================== -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="editModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="editModalOpen = false"
                     x-show="editModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-4xl rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <form :action="'{{ url('dashboard/learning-resources/templates') }}/' + editTemplateId" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Modal Header -->
                        <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <div>
                                <h5 class="text-base font-bold text-dark dark:text-white-light">Edit Master Template</h5>
                                <p class="text-xs text-white-dark mt-0.5">Customize contract terms, clauses, and layout spacing.</p>
                            </div>
                            <button type="button" 
                                    @click="editModalOpen = false" 
                                    class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-200 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white transition" 
                                    style="display: flex; align-items: center; justify-content: center;"
                                    title="Close Modal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Template Title *</label>
                                    <input type="text" name="title" x-model="editTemplateTitle" required class="form-input text-xs w-full">
                                </div>
                                <div x-show="editTemplateLayout === 'stamp'">
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Stamp Top Margin (mm) *</label>
                                    <input type="number" name="header_space_mm" x-model="editTemplateHeaderSpace" min="0" max="200" class="form-input text-xs w-full">
                                    <span class="text-[10px] text-white-dark">Default is 90mm for standard stamp headers.</span>
                                </div>
                            </div>

                            <!-- In-CRM Master Word (.docx) Letterhead Template Section -->
                            <div class="rounded-xl border border-dashed border-primary/50 bg-primary/5 p-4 dark:border-primary/40 dark:bg-primary/10">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-bold text-primary flex items-center gap-1.5">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Official Word (.docx) Template File
                                    </label>
                                    <a :href="'{{ url('dashboard/learning-resources/templates') }}/' + editTemplateId + '/download-docx'" 
                                       class="inline-flex items-center gap-1 text-xs text-primary hover:underline font-semibold"
                                       target="_blank">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download Current .docx
                                    </a>
                                </div>
                                <p class="text-[11px] text-white-dark mb-2.5">
                                    To update the official Word template file directly from the CRM, upload your updated .docx file here.
                                </p>
                                <input type="file" name="docx_template" accept=".docx" class="form-input text-xs w-full bg-white dark:bg-[#121e32]">
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Contract Content / Clauses (Web Preview &amp; PDF) *</label>
                                    <span class="text-[10px] text-primary">Supports standard HTML tags, tables, and lists</span>
                                </div>
                                <textarea name="content" x-model="editTemplateContent" rows="14" required 
                                          class="form-textarea font-mono text-xs w-full leading-relaxed rounded-lg border border-[#e0e6ed] p-3 dark:border-[#1b2e4b] dark:bg-[#0e1726]"></textarea>
                            </div>

                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" id="modal_is_active" value="1" :checked="editTemplateActive" class="form-checkbox">
                                <label for="modal_is_active" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Active (Visible for download)</label>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="editModalOpen = false" class="btn btn-outline-danger btn-sm text-xs">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm text-xs">Save Template Changes</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

    <!-- ========================================== -->
    <!-- MODAL 2: UPLOAD DOCUMENT (FULL VIEWPORT)   -->
    <!-- ========================================== -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="docModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="docModalOpen = false"
                     x-show="docModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <form action="{{ route('admin.learning-resources.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <h5 class="text-base font-bold text-dark dark:text-white-light">Upload Country Document</h5>
                            <button type="button" 
                                    @click="docModalOpen = false" 
                                    class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-200 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white transition" 
                                    style="display: flex; align-items: center; justify-content: center;"
                                    title="Close Modal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Document Title *</label>
                                <input type="text" name="title" placeholder="e.g. Malaysia EMGS Visa Checklist 2026" required class="form-input text-xs w-full">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Destination Country</label>
                                <select name="country_id" class="form-select text-xs w-full">
                                    <option value="">General / All Countries</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description / Notes</label>
                                <textarea name="description" rows="2" placeholder="Brief notes or instructions for counselors/students" class="form-textarea text-xs w-full"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Select File (PDF, Word, Excel, Image - max 25MB) *</label>
                                <input type="file" name="file" required class="form-input text-xs w-full file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="docModalOpen = false" class="btn btn-outline-danger btn-sm text-xs">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm text-xs">Upload Document</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

    <!-- ========================================== -->
    <!-- MODAL 3: ADD INTERVIEW PREP (FULL VIEWPORT)-->
    <!-- ========================================== -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="interviewModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="interviewModalOpen = false"
                     x-show="interviewModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <form action="{{ route('admin.learning-resources.interview-prep.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <h5 class="text-base font-bold text-dark dark:text-white-light">Add Interview Preparation Resource</h5>
                            <button type="button" 
                                    @click="interviewModalOpen = false" 
                                    class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-200 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white transition" 
                                    style="display: flex; align-items: center; justify-content: center;"
                                    title="Close Modal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Resource Title *</label>
                                <input type="text" name="title" placeholder="e.g. Malta Embassy Mock Interview Common Questions" required class="form-input text-xs w-full">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Destination Country</label>
                                <select name="country_id" class="form-select text-xs w-full">
                                    <option value="">General / All Countries</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Private YouTube Video or Google Drive Link</label>
                                <input type="url" name="video_url" placeholder="https://youtube.com/watch?v=... or https://drive.google.com/file/d/..." class="form-input text-xs w-full">
                                <span class="text-[10px] text-white-dark">Supports unlisted/private YouTube links or shared Google Drive links</span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">External Cloud Folder / Notes URL</label>
                                <input type="url" name="external_link" placeholder="https://drive.google.com/drive/folders/..." class="form-input text-xs w-full">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description / Key Q&A Points</label>
                                <textarea name="description" rows="3" placeholder="Key interview tips, dos and don'ts, or common pitfalls" class="form-textarea text-xs w-full"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Attach Written Guide / PDF (Optional)</label>
                                <input type="file" name="file" class="form-input text-xs w-full file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="interviewModalOpen = false" class="btn btn-outline-danger btn-sm text-xs">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm text-xs">Save Resource</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection
