@extends('admin.layouts.master')

@section('title', 'Edit Course')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">Edit Program / Course</h2>
            <p class="text-xs text-white-dark mt-1">Update specifications, intake schedules, and admission requirements for {{ $course->name }}.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to List
        </a>
    </div>

    @if($errors->any())
        <div class="mt-4 rounded bg-danger-light p-3.5 text-danger dark:bg-danger-dark-light">
            <ul class="list-disc pl-5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel mt-6">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Section 1: Core Details -->
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">1. Institution & Program Identification</h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">University / Institution <span class="text-danger">*</span></label>
                        <select name="university_id" class="form-select select2" required>
                            <option value="">Select University</option>
                            @foreach ($universities as $university)
                                <option value="{{ $university->id }}" {{ old('university_id', $course->university_id) == $university->id ? 'selected' : '' }}>
                                    {{ $university->name }} ({{ $university->country?->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Program / Course Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-input text-xs" placeholder="e.g. Bachelor of Science in Computer Science & AI" value="{{ old('name', $course->name) }}" required>
                    </div>
                </div>
            </div>

            <!-- Section 2: Academic Categorization (Study Area & Program Level) -->
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">2. Academic Categorization & Discipline</h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    
                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Program Level</label>
                        <select name="program_level" class="form-select text-xs">
                            <option value="">Select Program Level</option>
                            @foreach ($programLevels as $key => $label)
                                <option value="{{ $key }}" {{ old('program_level', $course->program_level) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Study Area (Faculty)</label>
                        <input type="text" name="study_area" list="study_area_list" class="form-input text-xs" placeholder="e.g. Engineering & Technology" value="{{ old('study_area', $course->study_area) }}">
                        <datalist id="study_area_list">
                            @foreach($studyAreas as $area)
                                <option value="{{ $area }}">
                            @endforeach
                        </datalist>
                        <span class="text-[10px] text-white-dark">Choose from suggestions or type a custom study area</span>
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Discipline Area (Major)</label>
                        <input type="text" name="discipline_area" class="form-input text-xs" placeholder="e.g. Software Engineering, Robotics" value="{{ old('discipline_area', $course->discipline_area) }}">
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Degree Title (Optional)</label>
                        <input type="text" name="degree_level" class="form-input text-xs" placeholder="e.g. B.Sc (Hons), MBA, M.Eng" value="{{ old('degree_level', $course->degree_level) }}">
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Duration Description</label>
                        <input type="text" name="duration" class="form-input text-xs" placeholder="e.g. 3 Years, 48 Months" value="{{ old('duration', $course->duration) }}">
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Duration Range (For Search Filter)</label>
                        <select name="duration_type" class="form-select text-xs">
                            <option value="">Select Duration Range</option>
                            <option value="less_than_1_year" {{ old('duration_type', $course->duration_type) == 'less_than_1_year' ? 'selected' : '' }}>Less than 1 Year</option>
                            <option value="1_2_years" {{ old('duration_type', $course->duration_type) == '1_2_years' ? 'selected' : '' }}>1 - 2 Years</option>
                            <option value="3_4_years" {{ old('duration_type', $course->duration_type) == '3_4_years' ? 'selected' : '' }}>3 - 4 Years</option>
                            <option value="4_plus_years" {{ old('duration_type', $course->duration_type) == '4_plus_years' ? 'selected' : '' }}>4+ Years</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Section 3: Tuition & Financials -->
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">3. Tuition Fees & Availability</h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Currency</label>
                        <select name="currency" class="form-select text-xs">
                            <option value="USD" {{ old('currency', $course->currency ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ old('currency', $course->currency ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="MYR" {{ old('currency', $course->currency ?? 'USD') == 'MYR' ? 'selected' : '' }}>MYR (RM)</option>
                            <option value="RUB" {{ old('currency', $course->currency ?? 'USD') == 'RUB' ? 'selected' : '' }}>RUB (₽)</option>
                            <option value="BDT" {{ old('currency', $course->currency ?? 'USD') == 'BDT' ? 'selected' : '' }}>BDT (৳)</option>
                            <option value="GBP" {{ old('currency', $course->currency ?? 'USD') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tuition Fee (Per Year / Total)</label>
                        <input type="number" step="0.01" name="tuition_fee" class="form-input text-xs" placeholder="e.g. 8500.00" value="{{ old('tuition_fee', $course->tuition_fee) }}">
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Program Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select text-xs" required>
                            <option value="1" {{ old('status', $course->status) == 1 ? 'selected' : '' }}>Active (Accepting Applications)</option>
                            <option value="0" {{ old('status', $course->status) == 0 ? 'selected' : '' }}>Inactive / Suspended</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 4: Intake Schedules -->
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">4. Available Intake Cycles</h5>
                
                @php
                    $currentIntakeMonths = old('available_intake_months', $course->available_intake_months ?? []);
                    if (!is_array($currentIntakeMonths)) {
                        $currentIntakeMonths = [];
                    }
                    $currentIntakeYears = old('intake_years', $course->intake_years ?? []);
                    if (!is_array($currentIntakeYears)) {
                        $currentIntakeYears = [];
                    }
                    $currentRequirements = old('requirements', $course->requirements ?? []);
                    if (!is_array($currentRequirements)) {
                        $currentRequirements = [];
                    }
                @endphp

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Available Intake Months</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                            <label class="flex items-center gap-1.5 border border-[#e0e6ed] rounded-lg px-3 py-1.5 text-xs cursor-pointer hover:bg-slate-50 dark:border-[#1b2e4b] dark:hover:bg-[#121e32]">
                                <input type="checkbox" name="available_intake_months[]" value="{{ $month }}" {{ in_array($month, $currentIntakeMonths) ? 'checked' : '' }} class="form-checkbox text-xs">
                                <span>{{ $month }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Active Intake Years</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['2025', '2026', '2027', '2028', '2029'] as $year)
                            <label class="flex items-center gap-1.5 border border-[#e0e6ed] rounded-lg px-3 py-1.5 text-xs cursor-pointer hover:bg-slate-50 dark:border-[#1b2e4b] dark:hover:bg-[#121e32]">
                                <input type="checkbox" name="intake_years[]" value="{{ $year }}" {{ in_array($year, $currentIntakeYears) ? 'checked' : '' }} class="form-checkbox text-xs">
                                <span>{{ $year }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Section 5: Admission Requirements, Waivers & Flags -->
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">5. Admission Requirements, Waivers & Badges</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($requirementsList as $reqKey => $reqLabel)
                        <label class="flex items-center gap-2 border border-[#e0e6ed] rounded-lg p-2.5 text-xs cursor-pointer hover:bg-slate-50 dark:border-[#1b2e4b] dark:hover:bg-[#121e32]">
                            <input type="checkbox" name="requirements[]" value="{{ $reqKey }}" {{ in_array($reqKey, $currentRequirements) ? 'checked' : '' }} class="form-checkbox">
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $reqLabel }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-6 pt-3 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                    <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer">
                        <input type="checkbox" name="esl_available" value="1" {{ old('esl_available', $course->esl_available) ? 'checked' : '' }} class="form-checkbox">
                        <span class="text-slate-700 dark:text-slate-300">ESL / ELP Available (English as a Second Language pathway)</span>
                    </label>

                    <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer">
                        <input type="checkbox" name="stem_program" value="1" {{ old('stem_program', $course->stem_program) ? 'checked' : '' }} class="form-checkbox">
                        <span class="text-slate-700 dark:text-slate-300">STEM Certified Program (Science, Technology, Engineering, Maths)</span>
                    </label>
                </div>
            </div>

            <!-- Section 6: Overview Description -->
            <div class="form-group mb-5">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Program Overview / Curriculum Description</label>
                <div id="editor-container" class="h-40 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b]"></div>
                <input type="hidden" name="description" id="description" value="{{ old('description', $course->description) }}">
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-danger text-xs px-6">Cancel</a>
                <button type="submit" class="btn btn-primary text-xs px-8">Update Program</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/quill.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Select University",
                allowClear: true,
                width: '100%'
            });

            // Initialize Quill
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Write program overview, syllabus, career prospects...',
            });

            // Set initial content
            @if(old('description', $course->description))
                quill.root.innerHTML = `{!! old('description', $course->description) !!}`;
            @endif

            $('form').on('submit', function() {
                $('#description').val(quill.root.innerHTML);
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/quill.snow.css') }}">
@endpush
