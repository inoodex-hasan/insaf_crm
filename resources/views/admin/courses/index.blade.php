@extends('admin.layouts.master')

@section('title', 'Courses')

@section('content')
<div x-data="coursesIndexHub()">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold uppercase">Courses</h2>
        @can('create-course')
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Add Course</a>
        @endcan
    </div>

    <div class="panel mt-6">
        <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
            <form action="{{ route('admin.courses.index') }}" method="GET"
                class="flex flex-1 flex-col gap-5 md:flex-row md:items-center w-full">
                <div class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search course name..."
                        class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5"
                                opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    <select name="university_id" class="form-select w-full md:w-80 pr-10">
                        <option value="">All Universities</option>
                        @foreach ($universities as $university)
                            <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                                {{ $university->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive" style="min-height: 280px;">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>University</th>
                        <th>Course Name</th>
                        <th>Study / Discipline Area</th>
                        <th>Degree Level</th>
                        <th>Duration</th>
                        <th>Tuition Fee</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $course->university->name ?? 'N/A' }}
                                </span>
                                @if($course->university?->country)
                                    <span class="block text-[11px] text-white-dark">
                                        {{ $course->university->country->name }}
                                        @if($course->university->state_province)
                                            ({{ $course->university->state_province }})
                                        @endif
                                    </span>
                                @endif
                            </td>
                            <td>
                                <button type="button" 
                                        @click="openCourseModal({{ json_encode($course) }}, {{ json_encode($course->university) }}, {{ json_encode($course->university?->country) }})"
                                        class="text-primary font-semibold hover:underline text-left"
                                        title="Click to view full course details">
                                    {{ Str::limit($course->name, 25) }}
                                </button>
                            </td>
                            <td>
                                @if($course->study_area || $course->discipline_area)
                                    <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $course->study_area ?? 'General' }}
                                    </span>
                                    <span class="block text-[11px] text-white-dark">
                                        {{ $course->discipline_area ?? '-' }}
                                    </span>
                                @else
                                    <span class="text-white-dark text-xs">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary/10 text-primary text-[11px] font-bold px-2 py-0.5 rounded">
                                    {{ $course->program_level_label }}
                                </span>
                            </td>
                            <td>{{ $course->duration ? $course->duration . ' Months' : 'N/A' }}</td>
                            <td>
                                <span class="font-bold text-success text-xs">
                                    {{ $course->formatted_fee }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $course->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $course->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                        <li>
                                            <button type="button" 
                                                    @click="openCourseModal({{ json_encode($course) }}, {{ json_encode($course->university) }}, {{ json_encode($course->university?->country) }}); toggle();" 
                                                    class="flex w-full items-center px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-dark/40">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                View
                                            </button>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.courses.edit', $course->id) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this course?');" class="m-0">
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
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $courses->links() }}
        </div>
    </div>

    <!-- VIEW COURSE MODAL - STRICT PROTOCOL (AGENTS.md RULE 1) -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="courseModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="courseModalOpen = false"
                     x-show="courseModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-3xl rounded-xl border border-[#e0e6ed] bg-white shadow-2xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden"
                     style="max-height: 90vh; display: flex; flex-direction: column;">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge bg-primary/10 text-primary text-[10px] font-extrabold uppercase px-2 py-0.5 rounded" 
                                      x-text="selectedCourse?.program_level ? selectedCourse.program_level.toUpperCase() : (selectedCourse?.degree_level || 'DEGREE')"></span>
                                <span class="badge" 
                                      :class="selectedCourse?.status ? 'bg-success/10 text-success border border-success/20' : 'bg-danger/10 text-danger border border-danger/20'"
                                      x-text="selectedCourse?.status ? 'Active' : 'Inactive'"></span>
                            </div>
                            <h5 class="text-base font-bold text-dark dark:text-white-light mt-1" x-text="selectedCourse?.name || 'Course Details'"></h5>
                        </div>
                        <button type="button" 
                                @click="courseModalOpen = false" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-dark hover:bg-slate-100 dark:hover:text-white dark:hover:bg-[#1b2e4b] transition">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable) -->
                    <div class="p-6 space-y-5 overflow-y-auto" style="flex: 1 1 auto; max-height: calc(90vh - 140px);">
                        
                        <!-- Quick Stats Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 dark:bg-[#121e32] p-4 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] text-xs">
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">University</span>
                                <span class="font-bold text-dark dark:text-white-light text-sm" x-text="selectedUniversity?.name || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">Country / Region</span>
                                <span class="font-bold text-dark dark:text-white-light text-sm" x-text="(selectedCountry?.name || 'N/A') + (selectedUniversity?.state_province ? ' (' + selectedUniversity.state_province + ')' : '')"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">Tuition Fee</span>
                                <span class="font-extrabold text-[#10b981] text-sm" x-text="selectedCourse?.tuition_fee ? (selectedCourse.currency || 'USD') + ' ' + Number(selectedCourse.tuition_fee).toLocaleString() : 'Contact University'"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block text-[10px] uppercase font-semibold">Duration</span>
                                <span class="font-bold text-dark dark:text-white-light text-sm" x-text="selectedCourse?.duration ? selectedCourse.duration + ' Months' : 'Standard Term'"></span>
                            </div>
                        </div>

                        <!-- Categorization / Faculty & Major -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3.5 rounded-lg bg-white dark:bg-[#1b2e4b]">
                                <span class="text-white-dark block text-[10px] uppercase font-semibold mb-1">Study Area (Faculty)</span>
                                <p class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCourse?.study_area || 'Not specified'"></p>
                            </div>
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3.5 rounded-lg bg-white dark:bg-[#1b2e4b]">
                                <span class="text-white-dark block text-[10px] uppercase font-semibold mb-1">Discipline Area (Major)</span>
                                <p class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCourse?.discipline_area || 'General'"></p>
                            </div>
                        </div>

                        <!-- Intake Cycles Schedule -->
                        <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-4 rounded-xl text-xs space-y-2 bg-white dark:bg-[#1b2e4b]">
                            <span class="text-white-dark block text-[10px] uppercase font-semibold">Available Intake Cycles</span>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-if="selectedCourse?.available_intake_months && selectedCourse.available_intake_months.length > 0">
                                    <template x-for="m in selectedCourse.available_intake_months" :key="m">
                                        <span class="badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-[11px] font-bold px-2.5 py-0.5 rounded" x-text="m"></span>
                                    </template>
                                </template>
                                <template x-if="!selectedCourse?.available_intake_months || selectedCourse.available_intake_months.length === 0">
                                    <span class="text-slate-400">All Year Round / Rolling Intakes</span>
                                </template>
                            </div>
                            <template x-if="selectedCourse?.intake_years && selectedCourse.intake_years.length > 0">
                                <div class="text-[11px] text-white-dark pt-1">
                                    Intake Years: <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="selectedCourse.intake_years.join(', ')"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Admission Requirements & Badges -->
                        <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-4 rounded-xl text-xs space-y-2 bg-white dark:bg-[#1b2e4b]">
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
                                <template x-if="(!selectedCourse?.requirements || selectedCourse.requirements.length === 0) && !selectedCourse?.stem_program && !selectedCourse?.esl_available">
                                    <span class="text-slate-400">Standard entry requirements apply.</span>
                                </template>
                            </div>
                        </div>

                        <!-- Program Description & Syllabus -->
                        <div>
                            <span class="text-white-dark block text-[10px] uppercase font-semibold mb-2">Program Overview & Syllabus</span>
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-4 rounded-xl text-xs leading-relaxed text-slate-700 dark:text-slate-300 max-h-52 overflow-y-auto bg-slate-50/50 dark:bg-[#121e32]" 
                                 x-html="selectedCourse?.description || '<p class=\'text-slate-400 italic\'>No extended description provided for this course.</p>'">
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between border-t border-[#e0e6ed] bg-[#fbfbfb] px-6 py-3.5 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <button type="button" @click="courseModalOpen = false" class="btn btn-outline-danger btn-sm text-xs px-5">
                            Close
                        </button>
                        <div class="flex items-center gap-2">
                            <a :href="'{{ url('dashboard/courses') }}/' + (selectedCourse?.id || '') + '/edit'" 
                               class="btn btn-primary btn-sm text-xs px-5 flex items-center gap-1.5">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                <span>Edit Course</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
    function coursesIndexHub() {
        return {
            courseModalOpen: false,
            selectedCourse: null,
            selectedUniversity: null,
            selectedCountry: null,

            openCourseModal(course, university, country) {
                this.selectedCourse = course;
                this.selectedUniversity = university;
                this.selectedCountry = country;
                this.courseModalOpen = true;
            }
        };
    }
</script>
@endpush
