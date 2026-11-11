@extends('admin.layouts.master')

@section('title', 'Search Programs & Courses')

@section('content')
<div x-data="searchProgramHub()" class="search-program-wrapper">

    <!-- Main Search Card matching Reference Screenshot Exactly -->
    <div class="search-program-card">
        <form method="GET" action="{{ route('admin.search-program.index') }}" id="searchProgramForm">
            
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
                            <select name="country_id">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Province | State (University Location) -->
                        <div class="filter-select-group">
                            <label>Province | State</label>
                            <select name="state_province">
                                <option value="">Select Province | State</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}" {{ request('state_province') == $prov ? 'selected' : '' }}>
                                        {{ $prov }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Study Area (Dynamic) -->
                        <div class="filter-select-group">
                            <label>Study Area</label>
                            <select name="study_area">
                                <option value="">Select Study Area</option>
                                @foreach($studyAreas as $area)
                                    <option value="{{ $area }}" {{ request('study_area') == $area ? 'selected' : '' }}>
                                        {{ $area }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Discipline Area (Dynamic) -->
                        <div class="filter-select-group">
                            <label>Discipline Area</label>
                            <select name="discipline_area">
                                <option value="">Select Discipline Area</option>
                                @foreach($disciplineAreas as $disc)
                                    <option value="{{ $disc }}" {{ request('discipline_area') == $disc ? 'selected' : '' }}>
                                        {{ $disc }}
                                    </option>
                                @endforeach
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
    <div class="results-container">
        
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

                            <!-- Course Title -->
                            <h4 class="course-card-title" 
                                @click="openDetailModal({{ json_encode($course) }}, {{ json_encode($course->university) }}, {{ json_encode($course->university?->country) }})">
                                {{ $course->name }}
                            </h4>

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

                                @if($course->duration)
                                    <div class="course-meta-item">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        <span>{{ $course->duration }}</span>
                                    </div>
                                @endif
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

                        <!-- Card Right Side: Fee & Action Buttons -->
                        <div class="course-card-side">
                            <div class="fee-block">
                                <span class="fee-label">Tuition Fee</span>
                                <span class="fee-amount">{{ $course->formatted_fee }}</span>
                            </div>

                            <div class="actions-block">
                                <button type="button" 
                                        @click="openDetailModal({{ json_encode($course) }}, {{ json_encode($course->university) }}, {{ json_encode($course->university?->country) }})"
                                        class="btn-detail-view">
                                    View Details
                                </button>

                                @can('*consultant|*application|*marketing')
                                    <a href="{{ route('admin.applications.create', ['university_id' => $course->university_id, 'course_id' => $course->id]) }}" 
                                       class="btn-apply-fast">
                                        <span>Apply</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </a>
                                @endcan
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $courses->links() }}
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
                                <span class="font-bold text-[#10b981]" x-text="selectedCourse?.tuition_fee ? (selectedCourse.currency || 'USD') + ' ' + Number(selectedCourse.tuition_fee).toLocaleString() : 'Contact University'"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">Duration</span>
                                <span class="font-bold text-dark dark:text-white-light" x-text="selectedCourse?.duration || 'Standard Term'"></span>
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
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
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
    @media (max-width: 768px) {
        .course-card {
            flex-direction: column;
            align-items: stretch;
        }
    }

    .course-card-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
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

    .actions-block {
        display: flex;
        align-items: center;
        gap: 8px;
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

            openDetailModal(course, university, country) {
                this.selectedCourse = course;
                this.selectedUniversity = university;
                this.selectedCountry = country;
                this.modalOpen = true;
            }
        };
    }
</script>
@endpush
