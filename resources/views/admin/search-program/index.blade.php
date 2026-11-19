@extends('admin.layouts.master')

@section('title', 'Search Programs & Courses')

@section('content')
<div x-data="searchProgramHub()" class="search-program-wrapper">

    <!-- Main Search Card matching Reference Screenshot Exactly -->
    <div class="search-program-card">
        <form method="GET" action="{{ route('admin.search-program.index') }}#search-results" id="searchProgramForm" @submit="sessionStorage.setItem('scroll_to_results', '1')">
            <input type="hidden" name="applied_search" value="1">
            
            <!-- Top Search Bar -->
            <div class="top-filter-bar">
                
                <!-- 1. Search Program / University -->
                <div class="filter-field-col" style="flex: 3.2; min-width: 250px;">
                    <label>Search Programs</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 21h18M3 10h18M5 10v11M19 10v11M9 10v11M15 10v11M12 3l9 5H3l9-5z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input type="text" 
                               name="keyword" 
                               value="{{ request('keyword') }}" 
                               placeholder="Search Program / University"
                               autocomplete="off">
                    </div>
                </div>

                <!-- 2. Intake Month -->
                <div class="filter-field-col" style="flex: 2.2; min-width: 170px;">
                    <label>Intake</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </span>
                        <select name="intake_month">
                            <option value="All" {{ request('intake_month') == 'All' || !request('intake_month') ? 'selected' : '' }}>All, Jan, Feb, Mar, Apr...</option>
                            @foreach($intakeMonths as $m)
                                @if($m !== 'All')
                                    <option value="{{ $m }}" {{ request('intake_month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 3. Intake Year -->
                <div class="filter-field-col" style="flex: 1.4; min-width: 120px;">
                    <label>Year</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </span>
                        <select name="intake_year">
                            <option value="All" {{ request('intake_year') == 'All' ? 'selected' : '' }}>All</option>
                            @foreach($intakeYears as $yr)
                                <option value="{{ $yr }}" {{ request('intake_year', '2027') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 4. Student's Nationality (Bangladesh default, student state excluded) -->
                <div class="filter-field-col" style="flex: 2; min-width: 160px;">
                    <label>Student's Nationality</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="4" y1="22" x2="4" y2="15"/>
                            </svg>
                        </span>
                        <select name="nationality">
                            <option value="Bangladesh" {{ request('nationality', 'Bangladesh') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                            <option value="India" {{ request('nationality') == 'India' ? 'selected' : '' }}>India</option>
                            <option value="Nepal" {{ request('nationality') == 'Nepal' ? 'selected' : '' }}>Nepal</option>
                            <option value="Pakistan" {{ request('nationality') == 'Pakistan' ? 'selected' : '' }}>Pakistan</option>
                            <option value="Nigeria" {{ request('nationality') == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                            <option value="Other" {{ request('nationality') == 'Other' ? 'selected' : '' }}>All Nationalities</option>
                        </select>
                    </div>
                </div>

                <!-- 5. Search Button -->
                <div class="filter-field-col" style="flex: 0 0 auto; min-width: 110px;">
                    <button type="submit" class="btn-search-green">
                        <span>Search</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Advanced Search Header with Left Manage Filters, Center Green Title & Right Clear All -->
            <div class="advanced-search-header">
                <div class="advanced-header-left">
                    <a href="{{ route('admin.program-settings.index') }}" class="manage-filters-link" title="Configure Program Levels & Admission Requirements">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span>Manage Filters</span>
                    </a>
                </div>

                <div class="advanced-header-center">
                    <button type="button" 
                            @click="advancedOpen = !advancedOpen" 
                            class="advanced-toggle-btn"
                            title="Click to toggle advanced search filters">
                        <span>Advanced Search</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" 
                             class="chevron-icon"
                             :style="advancedOpen ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                </div>

                <div class="advanced-header-right">
                    <a href="{{ route('admin.search-program.index') }}" class="clear-all-link">
                        <span>Clear All</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <polyline points="1 20 1 14 7 14"></polyline>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Collapsible 3 Columns Section -->
            <div x-show="advancedOpen" 
                 x-collapse 
                 x-cloak>
                <div class="advanced-columns-container">

                <!-- COLUMN 1: Program Level -->
                <div class="advanced-col">
                    <div class="advanced-col-title">Program Level</div>
                    @php
                        $selectedLevels = request('program_levels', []);
                        if (!is_array($selectedLevels)) {
                            $selectedLevels = [];
                        }
                    @endphp
                    <div class="checkbox-list">
                        @foreach($programLevels as $levelKey => $levelLabel)
                            <label class="checkbox-item">
                                <input type="checkbox" 
                                       name="program_levels[]" 
                                       value="{{ $levelKey }}" 
                                       {{ in_array($levelKey, $selectedLevels) ? 'checked' : '' }}>
                                <span>{{ $levelLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- COLUMN 2: Other Filters -->
                <div class="advanced-col">
                    <div class="advanced-col-title">Other Filters</div>
                    <div class="select-filters-list">

                        <!-- Country -->
                        <div class="filter-select-group">
                            <label>Country</label>
                            <select name="country_id" x-model="filterCountryId" @change="onCountryChange()">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Province | State (Cascades by Country when selected) -->
                        <div class="filter-select-group">
                            <label>Province | State</label>
                            <select name="state_province" x-model="filterProvince">
                                <option value="">Select Province | State</option>
                                <template x-for="prov in availableProvinces" :key="prov">
                                    <option :value="prov" x-text="prov" :selected="filterProvince === prov"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Study Area (Top-level Category) -->
                        <div class="filter-select-group">
                            <label>Study Area</label>
                            <select name="study_area" x-model="filterStudyArea" @change="onStudyAreaChange()">
                                <option value="">Select Study Area</option>
                                @foreach($studyAreas as $area)
                                    <option value="{{ $area }}">
                                        {{ $area }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Discipline Area (Subcategory - Greyed out until Study Area is picked) -->
                        <div class="filter-select-group">
                            <label>Discipline Area</label>
                            <select name="discipline_area"
                                    x-model="filterDisciplineArea"
                                    :disabled="!filterStudyArea"
                                    :title="filterStudyArea ? 'Select Discipline Area' : 'Pick a Study Area first to unlock'">
                                <option value="" x-text="filterStudyArea ? (availableDisciplines.length ? 'Select Discipline Area' : 'No specialties under this Study Area') : 'Select Study Area first'"></option>
                                <template x-for="disc in availableDisciplines" :key="disc">
                                    <option :value="disc" x-text="disc" :selected="filterDisciplineArea === disc"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Duration -->
                        <div class="filter-select-group">
                            <label>Duration</label>
                            <select name="duration_type">
                                <option value="">Select Duration</option>
                                @foreach($durations as $dKey => $dLabel)
                                    <option value="{{ $dKey }}" {{ request('duration_type') == $dKey ? 'selected' : '' }}>
                                        {{ $dLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ESL / ELP Available -->
                        <div class="pt-1">
                            <label class="checkbox-item" style="font-weight: 500;">
                                <input type="checkbox" 
                                       name="esl_available" 
                                       value="1" 
                                       {{ request('esl_available') ? 'checked' : '' }}>
                                <span>ESL / ELP Available</span>
                            </label>
                        </div>

                    </div>
                </div>

                <!-- COLUMN 3: Requirements -->
                <div class="advanced-col">
                    <div class="advanced-col-title">Requirements</div>
                    @php
                        $selectedReqs = request('requirements', []);
                        if (!is_array($selectedReqs)) {
                            $selectedReqs = [];
                        }
                    @endphp
                    <div class="checkbox-list">
                        @foreach($requirementsList as $reqKey => $reqLabel)
                            <label class="checkbox-item">
                                <input type="checkbox" 
                                       name="requirements[]" 
                                       value="{{ $reqKey }}" 
                                       {{ in_array($reqKey, $selectedReqs) ? 'checked' : '' }}>
                                <span>{{ $reqLabel }}</span>
                            </label>
                        @endforeach

                        <!-- STEM Programs Flag with Info Tooltip -->
                        <label class="checkbox-item" style="padding-top: 6px; border-top: 1px dashed #e2e8f0;">
                            <input type="checkbox" 
                                   name="stem_program" 
                                   value="1" 
                                   {{ request('stem_program') ? 'checked' : '' }}>
                            <span class="flex items-center gap-1">
                                STEM Programs
                                <span class="stem-info-bubble" title="Science, Technology, Engineering, Maths">ⓘ</span>
                            </span>
                        </label>
                    </div>
                </div>

                </div>

                <!-- Bottom Action Row inside Collapsible section -->
                <div class="advanced-actions-footer">
                    <a href="{{ route('admin.search-program.index') }}" class="btn-clear-flat">Reset Filters</a>
                    <button type="submit" class="btn-search-green" style="height: 38px; padding: 0 28px; width: auto;">
                        Apply Search
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- Search Results Section -->
    <div class="results-container" id="search-results">
        
        <!-- Results Counter & Status -->
        <div class="results-header-bar">
            <div>
                <h3 class="results-title">Found Programs & Courses</h3>
                <span class="results-count">
                    Showing <strong>{{ $courses->total() }}</strong> programs matching criteria
                </span>
            </div>

            @if(request()->hasAny(['keyword', 'intake_month', 'intake_year', 'country_id', 'state_province', 'program_levels', 'study_area', 'discipline_area', 'duration_type', 'requirements', 'esl_available', 'stem_program']))
                <a href="{{ route('admin.search-program.index') }}" class="active-filter-badge">
                    <span>Clear Active Filters</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </a>
            @endif
        </div>

        <!-- Course Cards List -->
        @if($courses->count() > 0)
            <div class="course-cards-list">
                @foreach($courses as $course)
                    <div class="course-card">
                        <div class="course-card-main">
                            
                            <!-- Badges Row -->
                            <div class="course-badges-row">
                                <span class="badge-pill badge-level">{{ $course->program_level_label }}</span>
                                @if($course->study_area)
                                    <span class="badge-pill badge-study-area">{{ $course->study_area }}</span>
                                @endif
                                @if($course->stem_program)
                                    <span class="badge-pill badge-stem">STEM Certified</span>
                                @endif
                                @if($course->esl_available)
                                    <span class="badge-pill badge-esl">ESL Available</span>
                                @endif
                            </div>

                            <!-- Course Title & Duration Row (Matching Reference Layout) -->
                            <div style="display: flex; align-items: baseline; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                                <h4 class="course-card-title" 
                                    @click="openDetailModal({{ json_encode($course) }}, {{ json_encode($course->university) }}, {{ json_encode($course->university?->country) }})">
                                    {{ $course->name }}
                                </h4>
                                <div class="course-duration-header">
                                    Duration: {{ $course->formatted_duration }}
                                </div>
                            </div>

                            <!-- University & Location Details -->
                            <div class="course-meta-row">
                                <div class="course-meta-item">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="font-semibold">{{ $course->university?->name ?? 'N/A' }}</span>
                                </div>

                                <div class="course-meta-item">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <span>
                                        {{ $course->university?->country?->name ?? 'N/A' }}
                                        @if($course->university?->state_province)
                                            ({{ $course->university->state_province }})
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- 5-Pill Costing Breakdown (Directly Matching Reference Image) -->
                            <div class="course-costing-row">
                                <div class="costing-box">
                                    <div class="costing-pill">Tuition Fee</div>
                                    <div class="costing-amount">{{ $course->formatted_tuition_display }}</div>
                                </div>
                                <div class="costing-box">
                                    <div class="costing-pill">Admission Fee</div>
                                    <div class="costing-amount">{{ $course->formatted_admission_display }}</div>
                                </div>
                                <div class="costing-box">
                                    <div class="costing-pill">Living Cost</div>
                                    <div class="costing-amount">{{ $course->formatted_living_display }}</div>
                                </div>
                                <div class="costing-box">
                                    <div class="costing-pill">Additional</div>
                                    <div class="costing-amount">{{ $course->formatted_additional_display }}</div>
                                </div>
                                <div class="costing-box">
                                    <div class="costing-pill">Scholarship</div>
                                    <div class="costing-amount">{{ $course->formatted_scholarship_display }}</div>
                                </div>
                            </div>

                            <!-- Intakes & Requirements Tags -->
                            <div class="course-tags-row">
                                @if(!empty($course->available_intake_months) && count($course->available_intake_months) > 0)
                                    <div class="tag-group">
                                        <span class="tag-label">Intakes:</span>
                                        <span class="tag-value">{{ implode(', ', $course->available_intake_months) }}</span>
                                        @if(!empty($course->intake_years))
                                            <span class="tag-years">({{ implode(', ', $course->intake_years) }})</span>
                                        @endif
                                    </div>
                                @endif

                                @if(!empty($course->requirements) && count($course->requirements) > 0)
                                    <div class="tag-group" style="margin-left: 14px;">
                                        <span class="tag-label">Req:</span>
                                        @foreach(array_slice($course->requirements, 0, 4) as $req)
                                            <span class="req-chip">{{ str_replace('_', ' ', $req) }}</span>
                                        @endforeach
                                        @if(count($course->requirements) > 4)
                                            <span class="req-chip-more">+{{ count($course->requirements) - 4 }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $courses->fragment('search-results')->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state-box">
                <div class="empty-state-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        <line x1="8" y1="11" x2="14" y2="11"/>
                    </svg>
                </div>
                <h3>No Matching Programs Found</h3>
                <p>Try clearing some criteria or expanding your intake year and country filters.</p>
                <div style="margin-top: 18px;">
                    <a href="{{ route('admin.search-program.index') }}" class="btn-search-green" style="height: 38px;">
                        Reset All Filters
                    </a>
                </div>
            </div>
        @endif

    </div>

    <!-- Course Detail Modal - STRICT PROTOCOL (RULE 1) -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="modalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="modalOpen = false"
                     x-show="modalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-2xl rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <div>
                            <span class="badge bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded" x-text="selectedCourse?.program_level ? selectedCourse.program_level.toUpperCase() : 'PROGRAM'"></span>
                            <h5 class="text-base font-bold text-dark dark:text-white-light mt-1" x-text="selectedCourse?.name"></h5>
                        </div>
                        <button type="button" 
                                @click="modalOpen = false" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-dark hover:bg-slate-100 dark:hover:text-white dark:hover:bg-[#1b2e4b] transition">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        
                        <!-- Quick Stats Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 dark:bg-[#121e32] p-4 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] text-xs">
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">Institution</span>
                                <span class="font-bold text-dark dark:text-white-light" x-text="selectedUniversity?.name || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">Country / State</span>
                                <span class="font-bold text-dark dark:text-white-light" x-text="(selectedCountry?.name || 'N/A') + (selectedUniversity?.state_province ? ' (' + selectedUniversity.state_province + ')' : '')"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">Tuition Fee</span>
                                <span class="font-bold text-[#10b981]" x-text="selectedCourse?.tuition_fee ? (selectedCourse.currency || 'USD') + ' ' + Number(selectedCourse.tuition_fee).toLocaleString() + '/' + (selectedCourse.fee_period || 'yr') : 'Contact University'"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">Duration</span>
                                <span class="font-bold text-dark dark:text-white-light" x-text="selectedCourse?.duration || (selectedCourse?.duration_type ? selectedCourse.duration_type.replace(/_/g, ' ') : 'N/A')"></span>
                            </div>
                        </div>

                        <!-- 5-Pill Costing Breakdown in Detail Modal -->
                        <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3.5 rounded-xl bg-white dark:bg-[#1b2e4b]">
                            <span class="text-white-dark block text-[10px] uppercase font-semibold mb-2">Financial Costing Breakdown</span>
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs">
                                <div class="bg-slate-50 dark:bg-[#121e32] p-2 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b]">
                                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Tuition Fee</span>
                                    <span class="font-bold text-primary text-xs" x-text="Number(selectedCourse?.tuition_fee) > 0 ? (selectedCourse.currency || 'USD') + ' ' + Number(selectedCourse.tuition_fee).toLocaleString() + '/' + (selectedCourse.fee_period || 'year') : 'N/A'"></span>
                                </div>
                                <div class="bg-slate-50 dark:bg-[#121e32] p-2 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b]">
                                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Admission</span>
                                    <span class="font-bold text-dark dark:text-white-light text-xs" x-text="Number(selectedCourse?.admission_fee) > 0 ? (selectedCourse.currency || 'USD') + ' ' + Number(selectedCourse.admission_fee).toLocaleString() : 'N/A'"></span>
                                </div>
                                <div class="bg-slate-50 dark:bg-[#121e32] p-2 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b]">
                                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Living Cost</span>
                                    <span class="font-bold text-dark dark:text-white-light text-xs" x-text="Number(selectedCourse?.living_cost) > 0 ? (selectedCourse.currency || 'USD') + ' ' + Number(selectedCourse.living_cost).toLocaleString() + '/' + (selectedCourse.fee_period || 'year') : 'N/A'"></span>
                                </div>
                                <div class="bg-slate-50 dark:bg-[#121e32] p-2 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b]">
                                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Additional</span>
                                    <span class="font-bold text-dark dark:text-white-light text-xs" x-text="Number(selectedCourse?.additional_cost) > 0 ? (selectedCourse.currency || 'USD') + ' ' + Number(selectedCourse.additional_cost).toLocaleString() : 'N/A'"></span>
                                </div>
                                <div class="bg-slate-50 dark:bg-[#121e32] p-2 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b]">
                                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Scholarship</span>
                                    <span class="font-bold text-success text-xs" x-text="Number(selectedCourse?.scholarship_amount) > 0 ? (selectedCourse.currency || 'USD') + ' ' + Number(selectedCourse.scholarship_amount).toLocaleString() + '/' + (selectedCourse.fee_period || 'year') : 'N/A'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Categorization Info -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3 rounded-lg">
                                <span class="text-white-dark block text-[10px] uppercase font-semibold mb-1">Study Area (Faculty)</span>
                                <p class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCourse?.study_area || 'Not specified'"></p>
                            </div>
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3 rounded-lg">
                                <span class="text-white-dark block text-[10px] uppercase font-semibold mb-1">Discipline Area (Major)</span>
                                <p class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCourse?.discipline_area || 'General'"></p>
                            </div>
                        </div>

                        <!-- Intakes Schedule -->
                        <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-4 rounded-lg text-xs space-y-2">
                            <span class="text-white-dark block text-[10px] uppercase font-semibold">Available Intake Cycles</span>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-if="selectedCourse?.available_intake_months && selectedCourse.available_intake_months.length > 0">
                                    <template x-for="m in selectedCourse.available_intake_months" :key="m">
                                        <span class="badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-[10px] font-bold px-2 py-0.5 rounded" x-text="m"></span>
                                    </template>
                                </template>
                                <template x-if="!selectedCourse?.available_intake_months || selectedCourse.available_intake_months.length === 0">
                                    <span class="text-slate-400">All Year Round / Rolling</span>
                                </template>
                            </div>
                        </div>

                        <!-- Admission Requirements -->
                        <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-4 rounded-lg text-xs space-y-2">
                            <span class="text-white-dark block text-[10px] uppercase font-semibold">Admission Requirements & Test Badges</span>
                            <div class="flex flex-wrap gap-2">
                                <template x-if="selectedCourse?.requirements && selectedCourse.requirements.length > 0">
                                    <template x-for="r in selectedCourse.requirements" :key="r">
                                        <span class="badge bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] font-bold px-2 py-1 rounded uppercase" x-text="r.replace('_', ' ')"></span>
                                    </template>
                                </template>
                                <template x-if="selectedCourse?.stem_program">
                                    <span class="badge bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 text-[10px] font-bold px-2 py-1 rounded">STEM CERTIFIED</span>
                                </template>
                                <template x-if="selectedCourse?.esl_available">
                                    <span class="badge bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-[10px] font-bold px-2 py-1 rounded">ESL / ELP AVAILABLE</span>
                                </template>
                            </div>
                        </div>

                        <!-- Description / Overview -->
                        <div>
                            <span class="text-white-dark block text-[10px] uppercase font-semibold mb-2">Program Overview & Syllabus</span>
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-4 rounded-lg text-xs leading-relaxed text-slate-700 dark:text-slate-300 max-h-48 overflow-y-auto" 
                                 x-html="selectedCourse?.description || '<p class=\'text-slate-400 italic\'>No extended description provided for this program.</p>'">
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <button type="button" @click="modalOpen = false" class="btn btn-outline-danger btn-sm text-xs px-5">
                            Close
                        </button>
                        @can('*consultant|*application|*marketing')
                            <a :href="'{{ url('dashboard/applications/create') }}?university_id=' + (selectedCourse?.university_id || '') + '&course_id=' + (selectedCourse?.id || '')" 
                               class="btn btn-primary btn-sm text-xs px-6 flex items-center gap-1.5">
                                <span>Create Student Application</span>
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @endcan
                    </div>

                </div>
            </div>
        </div>
    </template>

    <!-- 2. Costing Breakdown Modal - STRICT PROTOCOL (RULE 1) -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="costingModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="costingModalOpen = false"
                     x-show="costingModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-4xl rounded-xl border border-[#e0e6ed] bg-white shadow-2xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden"
                     style="max-height: 92vh; display: flex; flex-direction: column;">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded">Financial Breakdown</span>
                                <span class="text-xs font-semibold text-slate-500" x-text="costingCountry?.name || 'Destination'"></span>
                            </div>
                            <h5 class="text-base font-bold text-dark dark:text-white-light mt-0.5" x-text="costingCourse?.name || 'Course Costing Breakdown'"></h5>
                        </div>
                        <button type="button" 
                                @click="costingModalOpen = false" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-dark hover:bg-slate-100 dark:hover:text-white dark:hover:bg-[#1b2e4b] transition">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable) -->
                    <div class="p-6 space-y-5 overflow-y-auto" style="flex: 1 1 auto; max-height: calc(92vh - 145px);">
                        
                        <!-- Rate Adjuster & Program Banner -->
                        <div class="bg-slate-50 dark:bg-[#121e32] border border-[#e0e6ed] dark:border-[#1b2e4b] p-3.5 rounded-lg flex flex-wrap items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-700 dark:text-slate-300" x-text="costingUniversity?.name || 'Institution'"></span>
                                <span class="text-slate-400">•</span>
                                <span class="text-slate-500 font-medium" x-text="costingCourse?.duration || 'Term'"></span>
                            </div>

                            <!-- Live Exchange Rate Controls -->
                            <div class="flex items-center gap-2 bg-white dark:bg-[#1b2e4b] px-3 py-1.5 rounded border border-[#e0e6ed] dark:border-[#253b5c]">
                                <span class="text-white-dark font-semibold">Exchange Rate:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="'1 ' + costingCurrency + ' ='"></span>
                                <input type="number" 
                                       step="0.01" 
                                       min="0.1" 
                                       x-model.number="costingExchangeRate" 
                                       @input="recalculateAll()"
                                       class="form-input text-xs font-bold w-20 py-0.5 px-1.5 text-center border-slate-300 dark:border-slate-700 rounded text-emerald-600 dark:text-emerald-400">
                                <span class="font-semibold text-slate-600 dark:text-slate-400">BDT</span>
                            </div>
                        </div>

                        <!-- 4 MANDATORY CORE COST COMPONENTS -->
                        <div class="space-y-4">
                            
                            <!-- 1. TUITION FEE & SCHOLARSHIP SECTION -->
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-xl overflow-hidden">
                                <div class="bg-blue-50/60 dark:bg-blue-950/30 px-4 py-2.5 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between">
                                    <span class="text-xs font-bold text-blue-900 dark:text-blue-300 uppercase tracking-wide">
                                        1. Academic Tuition Fee & Scholarship
                                    </span>
                                    <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-400">Payable to: University</span>
                                </div>

                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                    <!-- A. Base Tuition Fee -->
                                    <div class="space-y-1.5 bg-slate-50 dark:bg-[#121e32] p-3 rounded-lg border border-slate-200 dark:border-[#1b2e4b]">
                                        <div class="flex items-center justify-between">
                                            <label class="font-bold text-slate-700 dark:text-slate-300">Base Tuition Fee</label>
                                            <span class="text-[10px] text-slate-400 uppercase font-semibold" x-text="costingCurrency"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   x-model.number="costingTuitionFee" 
                                                   @input="recalculateAll()"
                                                   class="form-input text-xs font-semibold py-1 px-2 border-slate-300 dark:border-[#253b5c] rounded">
                                            <span class="font-semibold text-slate-500 whitespace-nowrap">
                                                = BDT <span x-text="formatNumber(costingTuitionFee * costingExchangeRate)"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- B. Scholarship / Tuition Waiver -->
                                    <div class="space-y-1.5 bg-emerald-50/50 dark:bg-emerald-950/20 p-3 rounded-lg border border-emerald-200 dark:border-emerald-900/50">
                                        <div class="flex items-center justify-between">
                                            <label class="font-bold text-emerald-800 dark:text-emerald-300">
                                                Scholarship / Tuition Waiver (Deduction)
                                            </label>
                                            <span class="text-[10px] text-emerald-600 uppercase font-semibold">Minus (-)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   x-model.number="costingScholarshipAmount" 
                                                   @input="recalculateAll()"
                                                   placeholder="0.00"
                                                   class="form-input text-xs font-semibold py-1 px-2 border-emerald-300 dark:border-emerald-800 rounded text-emerald-700 dark:text-emerald-300">
                                            <span class="font-semibold text-emerald-700 dark:text-emerald-400 whitespace-nowrap">
                                                = -BDT <span x-text="formatNumber(costingScholarshipAmount * costingExchangeRate)"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Net Tuition Fee Subtotal Row -->
                                <div class="bg-blue-50/30 dark:bg-blue-950/20 px-4 py-2.5 border-t border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between text-xs font-bold">
                                    <span class="text-blue-900 dark:text-blue-300">Net Tuition Payable:</span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-slate-600 dark:text-slate-400" x-text="costingCurrency + ' ' + formatNumber(Math.max(0, costingTuitionFee - costingScholarshipAmount))"></span>
                                        <span class="text-blue-700 dark:text-blue-400 text-sm">BDT <span x-text="formatNumber(netTuitionBdt)"></span></span>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. UNIVERSITY APPLICATION FEE -->
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-xl overflow-hidden">
                                <div class="bg-amber-50/60 dark:bg-amber-950/30 px-4 py-2 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between">
                                    <span class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wide">
                                        2. University Application / Admission Fee
                                    </span>
                                    <span class="text-[11px] font-semibold text-amber-700 dark:text-amber-400">Payable to: University</span>
                                </div>

                                <div class="p-3 bg-white dark:bg-[#1b2e4b] flex flex-wrap items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2" style="flex: 1; min-width: 200px;">
                                        <input type="text" 
                                               x-model="costingApplicationTitle" 
                                               class="form-input text-xs py-1 px-2 border-slate-300 dark:border-[#253b5c] rounded"
                                               style="flex: 2;">
                                        <div class="flex items-center gap-1.5" style="flex: 1.5;">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   x-model.number="costingApplicationFee" 
                                                   @input="recalculateAll()"
                                                   class="form-input text-xs font-semibold py-1 px-2 border-slate-300 dark:border-[#253b5c] rounded text-right">
                                            <span class="font-bold text-slate-500" x-text="costingCurrency"></span>
                                        </div>
                                    </div>
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        BDT <span x-text="formatNumber(costingApplicationFee * costingExchangeRate)"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. ADDITIONAL COSTS (EMGS, STATE/CVU FEE, EMBASSY, MEDICAL, ETC.) -->
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-xl overflow-hidden">
                                <div class="bg-purple-50/60 dark:bg-purple-950/30 px-4 py-2 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between">
                                    <span class="text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wide">
                                        3. Additional Costs (EMGS, State Fee, CVU, Embassy & Legal)
                                    </span>
                                    <button type="button" 
                                            @click="addAdditionalItem()"
                                            class="text-[11px] font-bold text-purple-700 hover:text-purple-900 dark:text-purple-400 flex items-center gap-1 bg-white dark:bg-[#121e32] px-2.5 py-0.5 rounded border border-purple-200 dark:border-purple-800">
                                        <span>+ Add Cost</span>
                                    </button>
                                </div>

                                <div class="p-3 bg-white dark:bg-[#1b2e4b] space-y-2 text-xs">
                                    <template x-for="(item, idx) in costingAdditionalItems" :key="idx">
                                        <div class="flex flex-wrap items-center gap-2 p-2 rounded-lg bg-slate-50 dark:bg-[#121e32] border border-slate-200 dark:border-[#253b5c]">
                                            <!-- Title -->
                                            <div style="flex: 2.5; min-width: 170px;">
                                                <input type="text" 
                                                       x-model="item.title" 
                                                       placeholder="Cost title (e.g. EMGS Fee)"
                                                       class="form-input text-xs py-1 px-2 border-slate-300 dark:border-[#253b5c] rounded w-full">
                                            </div>

                                            <!-- Payable To -->
                                            <div style="flex: 1.5; min-width: 120px;">
                                                <select x-model="item.payable_to" 
                                                        class="form-select text-xs py-1 px-2 border-slate-300 dark:border-[#253b5c] rounded w-full">
                                                    <option value="government_embassy">Government / Embassy / EMGS</option>
                                                    <option value="university">University</option>
                                                    <option value="third_party">Third Party / Medical</option>
                                                    <option value="agency">Agency</option>
                                                </select>
                                            </div>

                                            <!-- Currency -->
                                            <div style="flex: 0.8; min-width: 70px;">
                                                <select x-model="item.currency" 
                                                        @change="onItemCurrencyChange(item)"
                                                        class="form-select text-xs py-1 px-2 border-slate-300 dark:border-[#253b5c] rounded w-full">
                                                    <option :value="costingCurrency" x-text="costingCurrency"></option>
                                                    <option value="BDT">BDT</option>
                                                    <option value="USD">USD</option>
                                                    <option value="EUR">EUR</option>
                                                    <option value="MYR">MYR</option>
                                                </select>
                                            </div>

                                            <!-- Amount -->
                                            <div style="flex: 1.2; min-width: 90px;">
                                                <input type="number" 
                                                       step="0.01" 
                                                       min="0" 
                                                       x-model.number="item.foreign_amount" 
                                                       @input="onItemAmountChange(item)"
                                                       class="form-input text-xs font-semibold py-1 px-2 border-slate-300 dark:border-[#253b5c] rounded w-full text-right">
                                            </div>

                                            <!-- BDT Equiv -->
                                            <div class="text-right font-bold text-slate-800 dark:text-slate-200" style="flex: 1.2; min-width: 90px;">
                                                BDT <span x-text="formatNumber(item.bdt_amount)"></span>
                                            </div>

                                            <!-- Remove button -->
                                            <div>
                                                <button type="button" 
                                                        @click="removeAdditionalItem(idx)" 
                                                        class="p-1 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-950/50">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Subtotal for Additional Costs -->
                                    <div class="flex items-center justify-between pt-1 px-1 font-bold text-xs">
                                        <span class="text-purple-900 dark:text-purple-300">Subtotal Additional Costs:</span>
                                        <span class="text-purple-700 dark:text-purple-400">BDT <span x-text="formatNumber(totalAdditionalBdt)"></span></span>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. INSAF AGENCY SERVICE PACKAGE -->
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-xl overflow-hidden">
                                <div class="bg-orange-50/60 dark:bg-orange-950/30 px-4 py-2 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between">
                                    <span class="text-xs font-bold text-orange-900 dark:text-orange-300 uppercase tracking-wide">
                                        4. Insaf Agency Service Package (Post-Approval / Service Charge)
                                    </span>
                                    <span class="text-[11px] font-semibold text-orange-700 dark:text-orange-400">Payable to: Insaf International</span>
                                </div>

                                <div class="p-3 bg-white dark:bg-[#1b2e4b] flex flex-wrap items-center justify-between gap-3 text-xs">
                                    <div class="text-slate-600 dark:text-slate-400">
                                        Official service fee agreed in file opening contract (payable upon visa approval).
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-700 dark:text-slate-300">BDT</span>
                                        <input type="number" 
                                               step="500" 
                                               min="0" 
                                               x-model.number="costingAgencyFee" 
                                               @input="recalculateAll()"
                                               class="form-input text-xs font-bold py-1 px-2 border-slate-300 dark:border-[#253b5c] rounded text-right w-32 text-orange-700 dark:text-orange-400">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- CORE SUMMARY KPI BAR -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-900 text-white p-4 rounded-xl shadow-inner text-xs">
                            <div class="text-center">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Net Tuition</span>
                                <span class="text-sm font-extrabold text-blue-300">BDT <span x-text="formatNumber(netTuitionBdt)"></span></span>
                            </div>
                            <div class="text-center">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">App & State/Addl</span>
                                <span class="text-sm font-extrabold text-amber-300">BDT <span x-text="formatNumber(totalAppAndAdditionalBdt)"></span></span>
                            </div>
                            <div class="text-center">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Agency Package</span>
                                <span class="text-sm font-extrabold text-orange-300">BDT <span x-text="formatNumber(costingAgencyFee)"></span></span>
                            </div>
                            <div class="text-center bg-emerald-600 rounded-lg py-1 px-2">
                                <span class="text-emerald-100 block text-[10px] uppercase font-bold tracking-wider">Grand Total Budget</span>
                                <span class="text-base font-black text-white">BDT <span x-text="formatNumber(grandTotalBdt)"></span></span>
                            </div>
                        </div>

                        <!-- ASSIGN TO STUDENT / CRM INTEGRATION PANEL -->
                        <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] bg-slate-50/70 dark:bg-[#121e32] p-4 rounded-xl text-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">
                                    Assign This Costing Breakdown to Active Student
                                </span>
                                <span class="text-[11px] text-slate-500">Links quotation to profile & saves history</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <!-- Student Selector -->
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Select Active Student:</label>
                                    <select x-model="selectedStudentId" class="form-select text-xs py-1.5">
                                        <option value="">-- Choose Student to Assign --</option>
                                        <template x-for="st in studentsList" :key="st.id">
                                            <option :value="st.id" x-text="st.first_name + ' ' + (st.last_name || '') + (st.passport_number ? ' (' + st.passport_number + ')' : '')"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Package Sync Toggle -->
                                <div class="flex flex-col justify-end">
                                    <label class="inline-flex items-center gap-2 cursor-pointer mt-auto pb-1">
                                        <input type="checkbox" x-model="syncPackageAmount" class="form-checkbox text-primary rounded">
                                        <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">Sync Agency Fee to Student Package Amount</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer & Action Buttons -->
                    <div class="flex flex-wrap items-center justify-between border-t border-[#e0e6ed] bg-[#fbfbfb] px-6 py-3.5 dark:border-[#1b2e4b] dark:bg-[#121e32] gap-3">
                        <button type="button" @click="costingModalOpen = false" class="btn btn-outline-danger btn-sm text-xs px-5">
                            Close
                        </button>

                        <div class="flex items-center gap-2">
                            <!-- 1. Print Quotation on Insaf Pad -->
                            <button type="button" 
                                    @click="printQuotation()" 
                                    class="btn btn-outline-primary btn-sm text-xs px-4 flex items-center gap-1.5">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                    <rect x="6" y="14" width="12" height="8"></rect>
                                </svg>
                                <span>Print Official Quotation</span>
                            </button>

                            <!-- 2. Assign to Student -->
                            <button type="button" 
                                    @click="saveStudentCosting()" 
                                    :disabled="!selectedStudentId || isSavingCosting"
                                    class="btn btn-success btn-sm text-xs px-5 flex items-center gap-1.5">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <span x-text="isSavingCosting ? 'Saving...' : 'Assign to Student'"></span>
                            </button>

                            <!-- 3. Direct Apply -->
                            @can('*consultant|*application|*marketing')
                                <a :href="'{{ url('dashboard/applications/create') }}?university_id=' + (costingCourse?.university_id || '') + '&course_id=' + (costingCourse?.id || '')" 
                                   class="btn btn-primary btn-sm text-xs px-5 flex items-center gap-1.5">
                                    <span>Apply</span>
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            @endcan
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('styles')
<style>
    /* Scoped styling to guarantee 100% pixel-perfect fidelity to reference design */
    .search-program-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .search-program-card {
        background: #ffffff;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .dark .search-program-card {
        background: #0e1726;
        border-color: #1b2e4b;
    }

    /* Top Search Bar */
    .top-filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        padding-bottom: 22px;
        border-bottom: 1px solid #e0e6ed;
    }
    .dark .top-filter-bar {
        border-bottom-color: #1b2e4b;
    }

    .filter-field-col {
        display: flex;
        flex-direction: column;
    }
    .filter-field-col label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .dark .filter-field-col label {
        color: #cbd5e1;
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }
    .input-with-icon .input-icon {
        position: absolute;
        left: 12px;
        color: #2563eb; /* Matching screenshot blue icon */
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    .input-with-icon input,
    .input-with-icon select {
        width: 100%;
        height: 42px;
        padding-left: 40px;
        padding-right: 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background-color: #ffffff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .dark .input-with-icon input,
    .dark .input-with-icon select {
        background-color: #121e32;
        border-color: #1b2e4b;
        color: #e2e8f0;
    }
    .input-with-icon input:focus,
    .input-with-icon select:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
    }

    .btn-search-green {
        background-color: #22c55e;
        color: #ffffff;
        font-weight: 600;
        font-size: 14px;
        height: 42px;
        padding: 0 24px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.2s;
    }
    .btn-search-green:hover {
        background-color: #16a34a;
    }

    /* Advanced Search Header */
    .advanced-search-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 0 14px;
        position: relative;
    }

    .advanced-header-left {
        flex: 1 1 0%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .advanced-header-center {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .advanced-header-right {
        flex: 1 1 0%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .manage-filters-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .manage-filters-link:hover {
        color: #0f172a;
        background-color: #e2e8f0;
        border-color: #94a3b8;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }
    .manage-filters-link svg {
        color: #64748b;
        transition: transform 0.3s ease;
    }
    .manage-filters-link:hover svg {
        color: #2563eb;
        transform: rotate(45deg);
    }
    .dark .manage-filters-link {
        color: #94a3b8;
        background-color: #1b2e4b;
        border-color: #2e4369;
    }
    .dark .manage-filters-link:hover {
        color: #e2e8f0;
        background-color: #243859;
        border-color: #3b82f6;
    }
    .dark .manage-filters-link svg {
        color: #64748b;
    }
    .dark .manage-filters-link:hover svg {
        color: #60a5fa;
    }

    .advanced-toggle-btn {
        background: transparent;
        border: none;
        color: #22c55e;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 8px;
        letter-spacing: 0.3px;
        transition: background-color 0.2s, color 0.2s;
    }
    .advanced-toggle-btn:hover {
        background-color: rgba(34, 197, 94, 0.08);
        color: #16a34a;
    }
    .advanced-toggle-btn .chevron-icon {
        transition: transform 0.25s ease;
    }

    .advanced-search-header .clear-all-link {
        color: #22c55e;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.2s, color 0.2s;
    }
    .advanced-search-header .clear-all-link:hover {
        background-color: rgba(34, 197, 94, 0.08);
        color: #16a34a;
        text-decoration: none;
    }

    @media (max-width: 640px) {
        .advanced-search-header {
            flex-direction: column;
            gap: 10px;
        }
        .advanced-header-left,
        .advanced-header-center,
        .advanced-header-right {
            width: 100%;
            justify-content: center;
        }
    }

    /* 3 Columns Layout */
    .advanced-columns-container {
        display: flex;
        gap: 36px;
        width: 100%;
        margin-top: 6px;
    }
    @media (max-width: 900px) {
        .advanced-columns-container {
            flex-direction: column;
            gap: 24px;
        }
    }
    .advanced-col {
        flex: 1;
        min-width: 0;
    }
    .advanced-col-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 14px;
    }
    .dark .advanced-col-title {
        color: #f1f5f9;
    }

    /* Checkboxes */
    .checkbox-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #475569;
        cursor: pointer;
        user-select: none;
        transition: color 0.15s;
    }
    .dark .checkbox-item {
        color: #cbd5e1;
    }
    .checkbox-item:hover {
        color: #1e293b;
    }
    .dark .checkbox-item:hover {
        color: #ffffff;
    }
    .checkbox-item input[type="checkbox"] {
        width: 17px;
        height: 17px;
        accent-color: #22c55e;
        border-radius: 4px;
        border: 1.5px solid #cbd5e1;
        cursor: pointer;
    }

    /* Column 2 Select List */
    .select-filters-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .filter-select-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .filter-select-group label {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
    }
    .dark .filter-select-group label {
        color: #94a3b8;
    }
    .filter-select-group select {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background-color: #ffffff;
        outline: none;
    }
    .dark .filter-select-group select {
        background-color: #121e32;
        border-color: #1b2e4b;
        color: #e2e8f0;
    }
    .filter-select-group select:focus {
        border-color: #22c55e;
    }
    .filter-select-group select:disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
        opacity: 0.75;
    }
    .dark .filter-select-group select:disabled {
        background-color: #0b1220;
        color: #475569;
        border-color: #17253d;
        cursor: not-allowed;
        opacity: 0.75;
    }

    .stem-info-bubble {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: bold;
    }
    .dark .stem-info-bubble {
        background-color: #1e293b;
        color: #94a3b8;
    }

    /* Actions Footer */
    .advanced-actions-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #e0e6ed;
    }
    .dark .advanced-actions-footer {
        border-top-color: #1b2e4b;
    }
    .btn-clear-flat {
        font-size: 13px;
        font-weight: 600;
        color: #ef4444;
        background: transparent;
        border: 1px solid #fca5a5;
        padding: 8px 18px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-clear-flat:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Results section */
    .results-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .results-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0 4px;
    }
    .results-title {
        font-size: 15px;
        font-weight: 700;
        text-transform: uppercase;
        color: #1e293b;
        margin: 0;
    }
    .dark .results-title {
        color: #f1f5f9;
    }
    .results-count {
        font-size: 12px;
        color: #64748b;
    }
    .results-count strong {
        color: #22c55e;
    }

    .active-filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #ef4444;
        background: #fee2e2;
        padding: 4px 12px;
        border-radius: 6px;
        text-decoration: none;
    }

    /* Course Cards */
    .course-cards-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .course-card {
        background: #ffffff;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 20px 24px;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .dark .course-card {
        background: #0e1726;
        border-color: #1b2e4b;
    }
    .course-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    .course-card-main {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .course-badges-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .badge-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        line-height: 1.2;
    }
    .badge-level {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        border: 1px solid rgba(37, 99, 235, 0.2);
    }
    .badge-study-area {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .dark .badge-study-area {
        background: #1e293b;
        color: #cbd5e1;
        border-color: #334155;
    }
    .badge-stem {
        background: #dcfce7;
        color: #166534;
    }
    .badge-esl {
        background: #dbeafe;
        color: #1e40af;
    }

    .course-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        cursor: pointer;
        transition: color 0.15s;
    }
    .dark .course-card-title {
        color: #f8fafc;
    }
    .course-card-title:hover {
        color: #2563eb;
    }

    .course-meta-row {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        font-size: 12px;
        color: #64748b;
    }
    .dark .course-meta-row {
        color: #94a3b8;
    }
    .course-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .course-meta-item svg {
        color: #94a3b8;
    }

    .course-tags-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        padding-top: 4px;
        font-size: 12px;
    }
    .tag-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .tag-label {
        font-weight: 700;
        color: #475569;
    }
    .dark .tag-label {
        color: #cbd5e1;
    }
    .tag-value {
        color: #64748b;
    }
    .tag-years {
        color: #94a3b8;
        font-size: 11px;
    }
    .req-chip {
        background: #f1f5f9;
        color: #475569;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .dark .req-chip {
        background: #1e293b;
        color: #cbd5e1;
    }
    .req-chip-more {
        font-size: 11px;
        color: #94a3b8;
    }

    /* Card Right Side */
    .course-card-side {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        gap: 12px;
        min-width: 190px;
        padding-left: 20px;
        border-left: 1px solid #e0e6ed;
    }
    .dark .course-card-side {
        border-left-color: #1b2e4b;
    }
    @media (max-width: 768px) {
        .course-card-side {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding-left: 0;
            padding-top: 14px;
            border-left: none;
            border-top: 1px solid #e0e6ed;
        }
    }

    .fee-block {
        text-align: right;
    }
    @media (max-width: 768px) {
        .fee-block {
            text-align: left;
        }
    }
    .fee-label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
    }
    .fee-amount {
        font-size: 16px;
        font-weight: 700;
        color: #22c55e;
    }

    .est-total-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 9999px;
        cursor: pointer;
        margin-top: 4px;
        transition: all 0.2s;
    }
    .dark .est-total-pill {
        background: rgba(6, 95, 70, 0.2);
        color: #34d399;
        border-color: rgba(5, 150, 105, 0.4);
    }
    .est-total-pill:hover {
        background: #d1fae5;
        transform: translateY(-1px);
    }

    .btn-costing-view {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #10b981;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .dark .btn-costing-view {
        background: rgba(6, 95, 70, 0.2);
        color: #34d399;
        border-color: #059669;
    }
    .btn-costing-view:hover {
        background: #10b981;
        color: #ffffff;
    }

    .actions-block {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* 5-Pill Costing Row (Matching Reference Image) */
    .course-costing-row {
        display: flex;
        gap: 16px;
        margin-top: 14px;
        flex-wrap: wrap;
    }
    .costing-box {
        flex: 1;
        min-width: 100px;
        text-align: center;
    }
    .costing-pill {
        background: #eaeff5;
        border: none;
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 8px;
        line-height: 1.2;
    }
    .dark .costing-pill {
        background: #1e293b;
        color: #e2e8f0;
    }
    .costing-amount {
        font-size: 16px;
        font-weight: 700;
        color: #0e3562;
        line-height: 1.2;
    }
    .dark .costing-amount {
        color: #93c5fd;
    }
    .course-duration-header {
        font-size: 15px;
        font-weight: 700;
        color: #0e3562;
        white-space: nowrap;
    }
    .dark .course-duration-header {
        color: #93c5fd;
    }
    .btn-detail-view {
        background: transparent;
        color: #2563eb;
        border: 1px solid #2563eb;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-detail-view:hover {
        background: #2563eb;
        color: #ffffff;
    }
    .btn-apply-fast {
        background: #2563eb;
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .btn-apply-fast:hover {
        background: #1d4ed8;
    }

    /* Empty state */
    .empty-state-box {
        background: #ffffff;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 48px 24px;
        text-align: center;
    }
    .dark .empty-state-box {
        background: #0e1726;
        border-color: #1b2e4b;
    }
    .empty-state-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .dark .empty-state-icon {
        background: #1e293b;
    }
    .empty-state-box h3 {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 6px 0;
    }
    .dark .empty-state-box h3 {
        color: #f1f5f9;
    }
    .empty-state-box p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    #search-results {
        scroll-margin-top: 80px;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }
</style>
@endpush

@push('scripts')
<script>
    function searchProgramHub() {
        return {
            advancedOpen: {{ request()->hasAny(['program_levels', 'country_id', 'state_province', 'study_area', 'discipline_area', 'duration_type', 'requirements', 'esl_available', 'stem_program']) ? 'true' : 'false' }},
            modalOpen: false,
            selectedCourse: null,
            selectedUniversity: null,
            selectedCountry: null,

            filterCountryId: @js((string) request('country_id', '')),
            filterProvince: @js((string) request('state_province', '')),
            filterStudyArea: @js((string) request('study_area', '')),
            filterDisciplineArea: @js((string) request('discipline_area', '')),
            studyDisciplineMap: @js($studyDisciplineMap),
            countryProvincesMap: @js($countryProvincesMap),
            allProvinces: @js($provinces),

            init() {
                // Re-sync dynamic x-for selects after initial DOM render
                this.$nextTick(() => {
                    const provVal = @js((string) request('state_province', ''));
                    const discVal = @js((string) request('discipline_area', ''));
                    if (provVal && this.availableProvinces.includes(provVal)) {
                        this.filterProvince = provVal;
                    }
                    if (discVal && this.availableDisciplines.includes(discVal)) {
                        this.filterDisciplineArea = discVal;
                    }
                });

                // Smoothly scroll down to results if search was applied or filters/pagination active
                const shouldScroll = sessionStorage.getItem('scroll_to_results') === '1' 
                    || window.location.hash === '#search-results'
                    || @js(request()->hasAny(['applied_search', 'keyword', 'country_id', 'state_province', 'program_levels', 'study_area', 'discipline_area', 'duration_type', 'requirements', 'esl_available', 'stem_program', 'page']));

                if (shouldScroll) {
                    sessionStorage.removeItem('scroll_to_results');
                    if ('scrollRestoration' in history) {
                        history.scrollRestoration = 'manual';
                    }

                    const doScroll = () => {
                        const target = document.getElementById('search-results');
                        if (target) {
                            const offset = 80;
                            const bodyRect = document.body.getBoundingClientRect().top;
                            const elementRect = target.getBoundingClientRect().top;
                            const elementPosition = elementRect - bodyRect;
                            const offsetPosition = elementPosition - offset;

                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });
                        }
                    };

                    this.$nextTick(() => doScroll());
                    setTimeout(doScroll, 120);
                    setTimeout(doScroll, 300);
                }
            },

            get availableDisciplines() {
                if (!this.filterStudyArea) return [];
                return this.studyDisciplineMap[this.filterStudyArea] || [];
            },

            get availableProvinces() {
                if (this.filterCountryId && this.countryProvincesMap[this.filterCountryId]) {
                    return this.countryProvincesMap[this.filterCountryId];
                }
                return this.allProvinces || [];
            },

            onStudyAreaChange() {
                if (!this.availableDisciplines.includes(this.filterDisciplineArea)) {
                    this.filterDisciplineArea = '';
                }
            },

            onCountryChange() {
                if (this.filterProvince && !this.availableProvinces.includes(this.filterProvince)) {
                    this.filterProvince = '';
                }
            },

            openDetailModal(course, university, country) {
                this.selectedCourse = course;
                this.selectedUniversity = university;
                this.selectedCountry = country;
                this.modalOpen = true;
            },

            // ==========================================
            // COSTING BREAKDOWN & FINANCIAL ENGINE
            // ==========================================
            costingModalOpen: false,
            costingCourse: null,
            costingUniversity: null,
            costingCountry: null,
            costingCurrency: 'USD',
            costingExchangeRate: 125.00,
            costingTuitionFee: 0,
            costingScholarshipAmount: 0,
            costingApplicationTitle: 'University Application / Admission Fee',
            costingApplicationFee: 100,
            costingAdditionalItems: [],
            costingAgencyFee: 50000,
            selectedStudentId: '',
            syncPackageAmount: true,
            isSavingCosting: false,

            costingTemplatesMap: @js($costingTemplatesMap),
            defaultCostingTemplate: @js($defaultCostingTemplate),
            studentsList: @js($studentsList),

            formatNumber(num) {
                if (num === null || num === undefined || isNaN(num)) return '0.00';
                return Number(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            get netTuitionForeign() {
                const tuition = Number(this.costingTuitionFee) || 0;
                const scholarship = Number(this.costingScholarshipAmount) || 0;
                return Math.max(0, tuition - scholarship);
            },

            get netTuitionBdt() {
                return this.netTuitionForeign * (Number(this.costingExchangeRate) || 1);
            },

            get totalAdditionalBdt() {
                return this.costingAdditionalItems.reduce((acc, item) => acc + (Number(item.bdt_amount) || 0), 0);
            },

            get totalAppAndAdditionalBdt() {
                const appBdt = (Number(this.costingApplicationFee) || 0) * (Number(this.costingExchangeRate) || 1);
                return appBdt + this.totalAdditionalBdt;
            },

            get grandTotalBdt() {
                return this.netTuitionBdt + this.totalAppAndAdditionalBdt + (Number(this.costingAgencyFee) || 0);
            },

            getEstTotalBdt(course, country) {
                const tpl = (country && this.costingTemplatesMap[country.id]) 
                    ? this.costingTemplatesMap[country.id] 
                    : this.defaultCostingTemplate;

                const rate = Number(tpl?.default_exchange_rate) || (course.currency === 'EUR' ? 135 : (course.currency === 'MYR' ? 30 : 125));
                const tuition = Math.max(0, (Number(course.tuition_fee) || 0) - (Number(course.scholarship_amount) || 0));
                const admission = Number(course.admission_fee) || 0;
                const living = Number(course.living_cost) || 0;
                const additional = Number(course.additional_cost) || 0;

                let total = (tuition + admission + living + additional) * rate;

                // Fallback for older courses with 0 values
                if (tuition === 0 && admission === 0 && living === 0 && additional === 0) {
                    total = 4500 * rate;
                }

                if (tpl && tpl.items) {
                    const agencyItem = tpl.items.find(i => i.category === 'agency_fee');
                    if (agencyItem) {
                        total += Number(agencyItem.default_bdt_amount) || 0;
                    }
                }

                return Math.round(total);
            },

            openCostingModal(course, university, country) {
                this.costingCourse = course;
                this.costingUniversity = university;
                this.costingCountry = country;

                const tpl = (country && this.costingTemplatesMap[country.id]) 
                    ? this.costingTemplatesMap[country.id] 
                    : this.defaultCostingTemplate;

                this.costingCurrency = course.currency || tpl?.currency || 'USD';
                this.costingExchangeRate = Number(tpl?.default_exchange_rate) || (this.costingCurrency === 'EUR' ? 135 : (this.costingCurrency === 'MYR' ? 30 : 125));
                
                // 1. Tuition Fee (Auto-pulled directly from course, or fallback to template)
                this.costingTuitionFee = Number(course.tuition_fee) > 0 
                    ? Number(course.tuition_fee) 
                    : (tpl?.items?.find(i => i.category === 'tuition')?.default_foreign_amount || 5000);

                // 2. Scholarship (Pulls from course scholarship_amount if set)
                this.costingScholarshipAmount = Number(course.scholarship_amount) > 0 
                    ? Number(course.scholarship_amount) 
                    : 0;

                // 3. Admission / Application Fee (Pulls from course admission_fee if set)
                const appItem = tpl?.items?.find(i => i.category === 'application_fee');
                this.costingApplicationTitle = appItem ? appItem.title : 'University Application / Admission Fee';
                this.costingApplicationFee = Number(course.admission_fee) > 0 
                    ? Number(course.admission_fee) 
                    : (appItem ? Number(appItem.default_foreign_amount) : (this.costingCurrency === 'MYR' ? 500 : 100));

                // 4. Agency Fee
                const agencyItem = tpl?.items?.find(i => i.category === 'agency_fee');
                this.costingAgencyFee = agencyItem ? Number(agencyItem.default_bdt_amount) : 50000;

                // 5. Additional Items (Living Cost, Insurance, EMGS, State fee, CVU, Embassy, Medical)
                this.costingAdditionalItems = [];

                // If course has living_cost saved, add it as a primary line item
                if (Number(course.living_cost) > 0) {
                    const lAmount = Number(course.living_cost);
                    this.costingAdditionalItems.push({
                        category: 'additional_cost',
                        title: 'Estimated Living Cost (' + (course.fee_period || 'year') + ')',
                        payable_to: 'third_party',
                        currency: this.costingCurrency,
                        foreign_amount: lAmount,
                        exchange_rate: this.costingExchangeRate,
                        bdt_amount: lAmount * this.costingExchangeRate,
                        is_deduction: false
                    });
                }

                // If course has additional_cost saved, add it
                if (Number(course.additional_cost) > 0) {
                    const addAmount = Number(course.additional_cost);
                    this.costingAdditionalItems.push({
                        category: 'additional_cost',
                        title: 'University Additional Fees & Insurance',
                        payable_to: 'university',
                        currency: this.costingCurrency,
                        foreign_amount: addAmount,
                        exchange_rate: this.costingExchangeRate,
                        bdt_amount: addAmount * this.costingExchangeRate,
                        is_deduction: false
                    });
                }

                // Append any country template additional costs (e.g. embassy / visa / legal fees)
                if (tpl && tpl.items) {
                    tpl.items.filter(i => i.category === 'additional_cost').forEach(i => {
                        const isBdt = i.currency === 'BDT';
                        const fAmount = Number(i.default_foreign_amount) || 0;
                        const bdtAmount = isBdt ? (Number(i.default_bdt_amount) || fAmount) : (fAmount * this.costingExchangeRate);

                        this.costingAdditionalItems.push({
                            category: 'additional_cost',
                            title: i.title,
                            payable_to: i.payable_to || 'government_embassy',
                            currency: i.currency || this.costingCurrency,
                            foreign_amount: fAmount,
                            exchange_rate: isBdt ? 1 : this.costingExchangeRate,
                            bdt_amount: bdtAmount,
                            is_deduction: false
                        });
                    });
                }

                this.selectedStudentId = '';
                this.costingModalOpen = true;
            },

            addAdditionalItem() {
                this.costingAdditionalItems.push({
                    category: 'additional_cost',
                    title: 'Embassy / State / Legal Fee',
                    payable_to: 'government_embassy',
                    currency: this.costingCurrency,
                    foreign_amount: 100,
                    exchange_rate: this.costingExchangeRate,
                    bdt_amount: 100 * this.costingExchangeRate,
                    is_deduction: false
                });
            },

            removeAdditionalItem(index) {
                this.costingAdditionalItems.splice(index, 1);
            },

            onItemAmountChange(item) {
                const amt = Number(item.foreign_amount) || 0;
                if (item.currency === 'BDT') {
                    item.bdt_amount = amt;
                } else {
                    item.bdt_amount = amt * (Number(this.costingExchangeRate) || 1);
                }
            },

            onItemCurrencyChange(item) {
                if (item.currency === 'BDT') {
                    item.exchange_rate = 1;
                    item.bdt_amount = Number(item.foreign_amount) || 0;
                } else {
                    item.exchange_rate = this.costingExchangeRate;
                    item.bdt_amount = (Number(item.foreign_amount) || 0) * this.costingExchangeRate;
                }
            },

            recalculateAll() {
                const rate = Number(this.costingExchangeRate) || 1;
                this.costingAdditionalItems.forEach(item => {
                    if (item.currency !== 'BDT') {
                        item.exchange_rate = rate;
                        item.bdt_amount = (Number(item.foreign_amount) || 0) * rate;
                    }
                });
            },

            buildAllItemsPayload() {
                const rate = Number(this.costingExchangeRate) || 1;
                const items = [];

                // 1. Base Tuition
                items.push({
                    category: 'tuition',
                    title: (this.costingCourse?.name || 'Course') + ' Tuition Fee',
                    payable_to: 'university',
                    currency: this.costingCurrency,
                    foreign_amount: Number(this.costingTuitionFee) || 0,
                    exchange_rate: rate,
                    bdt_amount: (Number(this.costingTuitionFee) || 0) * rate,
                    is_deduction: false
                });

                // 2. Scholarship
                if (Number(this.costingScholarshipAmount) > 0) {
                    items.push({
                        category: 'scholarship',
                        title: 'Scholarship / Tuition Fee Waiver',
                        payable_to: 'university',
                        currency: this.costingCurrency,
                        foreign_amount: Number(this.costingScholarshipAmount) || 0,
                        exchange_rate: rate,
                        bdt_amount: (Number(this.costingScholarshipAmount) || 0) * rate,
                        is_deduction: true
                    });
                }

                // 3. Application Fee
                if (Number(this.costingApplicationFee) > 0) {
                    items.push({
                        category: 'application_fee',
                        title: this.costingApplicationTitle,
                        payable_to: 'university',
                        currency: this.costingCurrency,
                        foreign_amount: Number(this.costingApplicationFee) || 0,
                        exchange_rate: rate,
                        bdt_amount: (Number(this.costingApplicationFee) || 0) * rate,
                        is_deduction: false
                    });
                }

                // 4. Additional Costs (EMGS, State fee, CVU, Embassy, etc.)
                this.costingAdditionalItems.forEach(item => {
                    items.push({
                        category: 'additional_cost',
                        title: item.title,
                        payable_to: item.payable_to,
                        currency: item.currency,
                        foreign_amount: Number(item.foreign_amount) || 0,
                        exchange_rate: item.currency === 'BDT' ? 1 : rate,
                        bdt_amount: Number(item.bdt_amount) || 0,
                        is_deduction: false
                    });
                });

                // 5. Agency Service Charge
                if (Number(this.costingAgencyFee) > 0) {
                    items.push({
                        category: 'agency_fee',
                        title: 'Insaf Agency Service Package (Post-Approval)',
                        payable_to: 'agency',
                        currency: 'BDT',
                        foreign_amount: Number(this.costingAgencyFee) || 0,
                        exchange_rate: 1,
                        bdt_amount: Number(this.costingAgencyFee) || 0,
                        is_deduction: false
                    });
                }

                return items;
            },

            printQuotation() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.search-program.print-quotation') }}';
                form.target = '_blank';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                const payload = {
                    course_name: this.costingCourse?.name,
                    university_name: this.costingUniversity?.name,
                    country_name: this.costingCountry?.name,
                    program_level: this.costingCourse?.program_level,
                    currency: this.costingCurrency,
                    exchange_rate: this.costingExchangeRate,
                    tuition_fee: Number(this.costingTuitionFee) || 0,
                    scholarship_amount: Number(this.costingScholarshipAmount) || 0,
                    net_tuition_fee: this.netTuitionBdt,
                    application_fee: (Number(this.costingApplicationFee) || 0) * (Number(this.costingExchangeRate) || 1),
                    total_additional_costs: this.totalAdditionalBdt,
                    agency_fee: Number(this.costingAgencyFee) || 0,
                    grand_total_bdt: this.grandTotalBdt,
                    items: this.buildAllItemsPayload(),
                    student_id: this.selectedStudentId || null
                };

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'quotation_data';
                input.value = JSON.stringify(payload);
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            },

            async saveStudentCosting() {
                if (!this.selectedStudentId) {
                    alert('Please select an active student to assign this costing breakdown.');
                    return;
                }

                this.isSavingCosting = true;
                const payload = {
                    student_id: this.selectedStudentId,
                    course_id: this.costingCourse?.id || null,
                    university_id: this.costingUniversity?.id || null,
                    country_id: this.costingCountry?.id || null,
                    currency: this.costingCurrency,
                    exchange_rate: Number(this.costingExchangeRate) || 1,
                    tuition_fee: Number(this.costingTuitionFee) || 0,
                    scholarship_amount: Number(this.costingScholarshipAmount) || 0,
                    net_tuition_fee: this.netTuitionBdt,
                    application_fee: (Number(this.costingApplicationFee) || 0) * (Number(this.costingExchangeRate) || 1),
                    total_additional_costs: this.totalAdditionalBdt,
                    agency_fee: Number(this.costingAgencyFee) || 0,
                    grand_total_bdt: this.grandTotalBdt,
                    sync_package_amount: this.syncPackageAmount,
                    items: this.buildAllItemsPayload(),
                    notes: 'Generated via Search Program for ' + (this.costingCourse?.name || 'Program')
                };

                try {
                    const response = await fetch('{{ route('admin.search-program.save-student-costing') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();
                    if (data.success) {
                        alert(data.message || 'Costing breakdown assigned successfully!');
                        this.costingModalOpen = false;
                    } else {
                        alert(data.message || 'Error saving costing breakdown.');
                    }
                } catch (err) {
                    console.error('Error saving costing breakdown:', err);
                    alert('Network error while saving costing breakdown.');
                } finally {
                    this.isSavingCosting = false;
                }
            }
        };
    }
</script>
@endpush
