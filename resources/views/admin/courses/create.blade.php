@extends('admin.layouts.master')

@section('title', 'Create Course')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">Create Program / Course</h2>
            <p class="text-xs text-white-dark mt-1">Add program specifications, study areas, intake cycles, and admission requirements.</p>
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
        <form action="{{ route('admin.courses.store') }}" method="POST">
            @csrf

            <!-- Section 1: Core Details -->
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">1. Institution & Program Identification</h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">University / Institution <span class="text-danger">*</span></label>
                        <select name="university_id" class="form-select select2" required>
                            <option value="">Select University</option>
                            @foreach ($universities as $university)
                                <option value="{{ $university->id }}" {{ old('university_id') == $university->id ? 'selected' : '' }}>
                                    {{ $university->name }} ({{ $university->country?->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Program / Course Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-input text-xs" value="{{ old('name') }}" required>
                    </div>
                </div>
            </div>

            <!-- Section 2: Academic Categorization (Study Area & Program Level) -->
            <div x-data="programLevelQuickAdd()" class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">2. Academic Categorization & Discipline</h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    
                    <div class="form-group">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Program Level</label>
                            <button type="button" @click="openModal()" class="text-[11px] font-semibold text-primary hover:underline flex items-center gap-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Level
                            </button>
                        </div>
                        <select name="program_level" id="program_level_select" class="form-select text-xs">
                            <option value="">Select Program Level</option>
                            @foreach ($programLevels as $key => $label)
                                <option value="{{ $key }}" {{ old('program_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quick Add Program Level Modal -->
                    <template x-teleport="#modal-portal">
                        <div x-cloak
                             x-show="isOpen"
                             class="modal-overlay-full"
                             style="display: none;">
                            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                                <div @click.away="isOpen = false"
                                     x-show="isOpen"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="w-full max-w-md rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                                        <h5 class="text-base font-bold text-dark dark:text-white-light">Add Program Level</h5>
                                        <button type="button" @click="isOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                        </button>
                                    </div>
                                    <form @submit.prevent="submitLevel()">
                                        <div class="p-5 space-y-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                                    Level Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" x-model="name" required class="form-input text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                                    Unique Code <span class="text-[10px] text-slate-400">(Optional - auto-generated if blank)</span>
                                                </label>
                                                <input type="text" x-model="code" class="form-input text-xs">
                                            </div>
                                            <template x-if="error">
                                                <div class="text-xs text-danger" x-text="error"></div>
                                            </template>
                                        </div>
                                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                                            <button type="button" @click="isOpen = false" class="btn btn-outline-danger btn-sm text-xs">Cancel</button>
                                            <button type="submit" :disabled="loading" class="btn btn-primary btn-sm text-xs">
                                                <span x-show="!loading">Save & Select Level</span>
                                                <span x-show="loading">Saving...</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Study Area (Faculty)</label>
                        <input type="text" name="study_area" class="form-input text-xs" value="{{ old('study_area') }}">
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Discipline Area (Major)</label>
                        <input type="text" name="discipline_area" class="form-input text-xs" value="{{ old('discipline_area') }}">
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Degree Title (Optional)</label>
                        <input type="text" name="degree_level" class="form-input text-xs" value="{{ old('degree_level') }}">
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Duration Description</label>
                        <input type="text" name="duration" class="form-input text-xs" value="{{ old('duration') }}">
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Duration Range (For Search Filter)</label>
                        <select name="duration_type" class="form-select text-xs">
                            <option value="">Select Duration Range</option>
                            <option value="less_than_1_year" {{ old('duration_type') == 'less_than_1_year' ? 'selected' : '' }}>Less than 1 Year</option>
                            <option value="1_2_years" {{ old('duration_type') == '1_2_years' ? 'selected' : '' }}>1 - 2 Years</option>
                            <option value="3_4_years" {{ old('duration_type') == '3_4_years' ? 'selected' : '' }}>3 - 4 Years</option>
                            <option value="4_plus_years" {{ old('duration_type') == '4_plus_years' ? 'selected' : '' }}>4+ Years</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Section 3: Tuition & Financials -->
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">3. Financial Costing Breakdown & Status</h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3 mb-4">
                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Currency</label>
                        <select name="currency" class="form-select text-xs">
                            <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD (C$)</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                            <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD (A$)</option>
                            <option value="MYR" {{ old('currency') == 'MYR' ? 'selected' : '' }}>MYR (RM)</option>
                            <option value="RUB" {{ old('currency') == 'RUB' ? 'selected' : '' }}>RUB (₽)</option>
                            <option value="BDT" {{ old('currency') == 'BDT' ? 'selected' : '' }}>BDT (৳)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Fee Period / Frequency</label>
                        <select name="fee_period" class="form-select text-xs">
                            <option value="year" {{ old('fee_period', 'year') == 'year' ? 'selected' : '' }}>Per Year (/year)</option>
                            <option value="semester" {{ old('fee_period') == 'semester' ? 'selected' : '' }}>Per Semester (/sem)</option>
                            <option value="month" {{ old('fee_period') == 'month' ? 'selected' : '' }}>Per Month (/mo)</option>
                            <option value="total" {{ old('fee_period') == 'total' ? 'selected' : '' }}>Total Program Fee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Program Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select text-xs" required>
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active (Accepting Applications)</option>
                            <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive / Suspended</option>
                        </select>
                    </div>
                </div>

                <!-- 5 Core Costing Breakdown Fields -->
                <div class="rounded-xl border border-[#e0e6ed] bg-[#fbfbfb] p-4 dark:border-[#1b2e4b] dark:bg-[#121e32] mb-2">
                    <div class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-3">
                        Cost Breakdown Items (Shown on Course Card)
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                        <div class="form-group">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tuition Fee</label>
                            <input type="text" name="tuition_fee" class="form-input text-xs" value="{{ old('tuition_fee') }}">
                        </div>

                        <div class="form-group">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Admission Fee</label>
                            <input type="text" name="admission_fee" class="form-input text-xs" value="{{ old('admission_fee') }}">
                        </div>

                        <div class="form-group">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Living Cost</label>
                            <input type="text" name="living_cost" class="form-input text-xs" value="{{ old('living_cost') }}">
                        </div>

                        <div class="form-group">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Additional</label>
                            <input type="text" name="additional_cost" class="form-input text-xs" value="{{ old('additional_cost') }}">
                        </div>

                        <div class="form-group">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Scholarship</label>
                            <input type="text" name="scholarship_amount" class="form-input text-xs" value="{{ old('scholarship_amount') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Intake Schedules -->
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">4. Available Intake Cycles</h5>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Available Intake Months</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                            <label class="flex items-center gap-1.5 border border-[#e0e6ed] rounded-lg px-3 py-1.5 text-xs cursor-pointer hover:bg-slate-50 dark:border-[#1b2e4b] dark:hover:bg-[#121e32]">
                                <input type="checkbox" name="available_intake_months[]" value="{{ $month }}" {{ in_array($month, old('available_intake_months', [])) ? 'checked' : '' }} class="form-checkbox text-xs">
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
                                <input type="checkbox" name="intake_years[]" value="{{ $year }}" {{ in_array($year, old('intake_years', ['2026', '2027'])) ? 'checked' : '' }} class="form-checkbox text-xs">
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
                            <input type="checkbox" name="requirements[]" value="{{ $reqKey }}" {{ in_array($reqKey, old('requirements', [])) ? 'checked' : '' }} class="form-checkbox">
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $reqLabel }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-6 pt-3 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                    <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer">
                        <input type="checkbox" name="esl_available" value="1" {{ old('esl_available') ? 'checked' : '' }} class="form-checkbox">
                        <span class="text-slate-700 dark:text-slate-300">ESL / ELP Available (English as a Second Language pathway)</span>
                    </label>

                    <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer">
                        <input type="checkbox" name="stem_program" value="1" {{ old('stem_program') ? 'checked' : '' }} class="form-checkbox">
                        <span class="text-slate-700 dark:text-slate-300">STEM Certified Program (Science, Technology, Engineering, Maths)</span>
                    </label>
                </div>
            </div>

            <!-- Section 6: Overview Description -->
            <div class="form-group mb-5">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Program Overview / Curriculum Description</label>
                <div id="editor-container" class="h-40 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b]"></div>
                <input type="hidden" name="description" id="description">
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-danger text-xs px-6">Cancel</a>
                <button type="submit" class="btn btn-primary text-xs px-8">Save Program</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/quill.js') }}"></script>
    <script>
        function programLevelQuickAdd() {
            return {
                isOpen: false,
                name: '',
                code: '',
                loading: false,
                error: '',
                openModal() {
                    this.name = '';
                    this.code = '';
                    this.error = '';
                    this.loading = false;
                    this.isOpen = true;
                },
                async submitLevel() {
                    if (!this.name.trim()) return;
                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('{{ route('admin.program-settings.levels.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                name: this.name,
                                code: this.code
                            })
                        });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.error = data.message || 'Failed to save program level.';
                            this.loading = false;
                            return;
                        }

                        // Add new level to select and select it
                        const select = document.getElementById('program_level_select');
                        if (select && data.level) {
                            const opt = document.createElement('option');
                            opt.value = data.level.code;
                            opt.textContent = data.level.name;
                            opt.selected = true;
                            select.appendChild(opt);
                            select.value = data.level.code;
                        }

                        this.isOpen = false;
                        if (typeof window.showMessage === 'function') {
                            window.showMessage(data.message || 'Program level created successfully.');
                        }
                    } catch (err) {
                        this.error = 'Network error. Please try again.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Select University",
                allowClear: true,
                width: '100%'
            });

            // Initialize Quill
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: '',
            });

            @if(old('description'))
                quill.root.innerHTML = `{!! old('description') !!}`;
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
