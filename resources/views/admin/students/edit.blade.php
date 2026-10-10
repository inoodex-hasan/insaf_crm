@extends('admin.layouts.master')

@section('title', 'Edit Student')

@push('styles')
    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid #e0e6ed !important;
            border-radius: 6px !important;
            padding: 6px 12px !important;
            display: flex;
            align-items: center;
            background-color: #fff;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0e1726;
            font-size: 14px;
            line-height: normal;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 10px;
        }

        .select2-dropdown {
            border-radius: 6px !important;
            border: 1px solid #e0e6ed !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: #fff;
            z-index: 9999;
        }

        .select2-search__field {
            padding: 6px !important;
            border-radius: 4px !important;
            border: 1px solid #e0e6ed !important;
            background-color: #fff !important;
            color: #0e1726 !important;
        }

        .select2-results__option {
            padding: 8px 12px;
            font-size: 14px;
        }

        .select2-results__option--highlighted {
            background-color: #4361ee !important;
            color: #fff !important;
        }

        .select2-results__option--selected {
            background-color: #e0e6ed !important;
            color: #0e1726 !important;
        }

        /* Dark mode support */
        .dark .select2-container .select2-selection--single {
            background-color: #1b2e4b !important;
            border-color: #253b5c !important;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #bfc9d4 !important;
        }

        .dark .select2-dropdown {
            background-color: #1b2e4b !important;
            border-color: #253b5c !important;
            color: #bfc9d4 !important;
        }

        .dark .select2-search__field {
            background-color: #121e32 !important;
            border-color: #253b5c !important;
            color: #bfc9d4 !important;
        }

        .dark .select2-results__option--selected {
            background-color: #253b5c !important;
            color: #fff !important;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #888ea8;
        }
    </style>
@endpush

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Edit Student</h2>
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to List
        </a>
    </div>

    <div class="panel mt-6">
        <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="form-group">
                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name" class="form-input" required
                        value="{{ old('first_name', $student->first_name) }}" />
                    @error('first_name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" id="last_name" class="form-input" required
                        value="{{ old('last_name', $student->last_name) }}" />
                    @error('last_name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="father_name">Father's Name</label>
                    <input type="text" name="father_name" id="father_name" class="form-input"
                        value="{{ old('father_name', $student->father_name) }}" />
                    @error('father_name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="mother_name">Mother's Name</label>
                    <input type="text" name="mother_name" id="mother_name" class="form-input"
                        value="{{ old('mother_name', $student->mother_name) }}" />
                    @error('mother_name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" id="phone" class="form-input" required
                        value="{{ old('phone', $student->phone) }}" />
                    @error('phone') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="sponsor_phone">Sponsor Phone</label>
                    <input type="text" name="sponsor_phone" id="sponsor_phone" class="form-input"
                        value="{{ old('sponsor_phone', $student->sponsor_phone) }}" />
                    @error('sponsor_phone') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="passport_number">Passport Number</label>
                    <input type="text" name="passport_number" id="passport_number" class="form-input"
                        value="{{ old('passport_number', $student->passport_number) }}" />
                    @error('passport_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="passport_validity">Passport Validity</label>
                    <input type="date" name="passport_validity" id="passport_validity" class="form-input"
                        value="{{ old('passport_validity', $student->passport_validity ? \Carbon\Carbon::parse($student->passport_validity)->format('Y-m-d') : '') }}" />
                    @error('passport_validity') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-input"
                        value="{{ old('email', $student->email) }}" />
                    @error('email') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password (Leave blank to keep current)</label>
                    <input type="text" name="password" id="password" class="form-input"
                        value="{{ old('password', $student->plain_password) }}" />
                    @error('password') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group md:col-span-2">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-textarea"
                        rows="2">{{ old('address', $student->address) }}</textarea>
                    @error('address') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-input"
                        value="{{ old('dob', $student->dob ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : '') }}" />
                    @error('dob') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="country_id">Target Country</label>
                    <select name="country_id" id="country_id" class="form-select select2">
                        <option value="">Select Country</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $student->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
 
                <div class="form-group">
                    <label for="university_id">Preferred University</label>
                    <select name="university_id" id="university_id" class="form-select select2">
                        <option value="">Select University</option>
                        @foreach ($universities as $university)
                            <option value="{{ $university->id }}" data-country-id="{{ $university->country_id }}"
                                {{ old('university_id', $student->university_id) == $university->id ? 'selected' : '' }}>
                                {{ $university->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('university_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="course_id">Preferred Course</label>
                    <select name="course_id" id="course_id" class="form-select select2">
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="course_intake_id">Preferred Intake</label>
                    <select name="course_intake_id" id="course_intake_id" class="form-select select2">
                        <option value="">Select Intake</option>
                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                            <option value="{{ $month }}"
                                {{ old('course_intake_id', optional($student->intake)->intake_name) === $month ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_intake_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" class="form-input"
                        value="{{ old('subject', $student->subject) }}" />
                    @error('subject') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="package_amount">Package Amount (Optional)</label>
                    <input type="number" step="0.01" min="0" name="package_amount" id="package_amount" class="form-input"
                        placeholder="Enter package amount (e.g. 50000.00)" value="{{ old('package_amount', $student->package_amount) }}" />
                    @error('package_amount') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group md:col-span-2">
                    <label for="documents">Upload Additional Documents</label>
                    <input type="file" name="documents[]" id="documents" class="form-input" multiple />
                    <span class="text-xs text-white-dark">Multiple documents can be uploaded (PDF, DOC, JPG, PNG). Max 10MB per file.</span>
                    @error('documents.*') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group md:col-span-2">
                    <label for="translation_documents">Upload Additional Translation Documents (Optional)</label>
                    <input type="file" name="translation_documents[]" id="translation_documents" class="form-input" multiple />
                    <span class="text-xs text-white-dark">Multiple translation documents can be uploaded (PDF, DOC, JPG, PNG). Max 10MB per file.</span>
                    @error('translation_documents.*') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <button type="button" onclick="window.location.href='{{ route('admin.students.index') }}'"
                    class="btn btn-outline-danger">Cancel</button>
                <button type="submit" class="btn btn-primary px-10">Update</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#country_id').select2({
                placeholder: "Select Country",
                allowClear: true,
                width: '100%'
            });

            $('#university_id').select2({
                placeholder: "Select University",
                allowClear: true,
                width: '100%'
            });

            $('#course_id').select2({
                placeholder: "Select Course",
                allowClear: true,
                width: '100%'
            });

            $('#course_intake_id').select2({
                placeholder: "Select Intake",
                allowClear: true,
                width: '100%'
            });

            // Handle Country selection
            $('#country_id').on('change', function() {
                const countryId = $(this).val();

                $('#university_id').html('<option value="">Select University</option>').trigger('change.select2');
                $('#course_id').html('<option value="">Select Course</option>').trigger('change.select2');

                if (countryId) {
                    $('#university_id').html('<option value="">Loading...</option>').trigger('change.select2');
                    $.ajax({
                        url: "{{ route('admin.applications.get-universities') }}",
                        type: "GET",
                        data: { country_id: countryId },
                        dataType: "json",
                        success: function(data) {
                            let options = '<option value="">Select University</option>';
                            $.each(data, function(index, university) {
                                options += `<option value="${university.id}" data-country-id="${countryId}">${university.name}</option>`;
                            });
                            $('#university_id').html(options).trigger('change.select2');
                        },
                        error: function() {
                            $('#university_id').html('<option value="">Select University</option>').trigger('change.select2');
                        }
                    });
                }
            });

            // Handle University selection
            $('#university_id').on('change', function() {
                const universityId = $(this).val();
                const selectedOption = $(this).find('option:selected');
                const countryId = selectedOption.data('country-id');

                // Auto-select Country if not already selected
                if (countryId && $('#country_id').val() != countryId) {
                    $('#country_id').val(countryId).trigger('change.select2');
                }

                $('#course_id').html('<option value="">Select Course</option>').trigger('change.select2');

                if (universityId) {
                    $('#course_id').html('<option value="">Loading...</option>').trigger('change.select2');
                    $.ajax({
                        url: "{{ route('admin.applications.get-courses') }}",
                        type: "GET",
                        data: { university_id: universityId },
                        dataType: "json",
                        success: function(data) {
                            let options = '<option value="">Select Course</option>';
                            if (data.length === 0) {
                                options = '<option value="" disabled>No courses available</option>';
                            } else {
                                $.each(data, function(index, course) {
                                    options += `<option value="${course.id}">${course.name}</option>`;
                                });
                            }
                            $('#course_id').html(options).trigger('change.select2');
                        },
                        error: function() {
                            $('#course_id').html('<option value="">Select Course</option>').trigger('change.select2');
                        }
                    });
                }
            });
        });
    </script>
@endpush
