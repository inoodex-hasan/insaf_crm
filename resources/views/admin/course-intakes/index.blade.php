@extends('admin.layouts.master')

@section('title', 'Course Intakes')

@section('content')

<div x-data="intakeModalHandler()">

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-semibold uppercase">Course Intakes</h2>
        <button type="button" @click="openCreateModal()" class="btn btn-primary">
            Add Intake
        </button>
    </div>

    <div class="panel mt-6">
        <!-- Toolbar: KPI summary & Search -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalCount ?? $intakes->total() }} Intakes
                </span>
                <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Active: {{ $activeCount ?? 0 }}
                </span>
            </div>

            <form action="{{ route('admin.course-intakes.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                <select name="course_id" class="form-select text-xs" style="height: 36px; width: 220px; border-radius: 6px;" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->university->name ?? '' }} - {{ $course->name }}
                        </option>
                    @endforeach
                </select>

                <div style="position: relative; width: 220px;">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search intake name..."
                           class="form-input text-xs w-full"
                           style="height: 36px; padding-right: 36px; border-radius: 6px;" />
                    <button type="submit"
                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; cursor: pointer;"
                            class="text-gray-400 hover:text-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>

                @if(request('search') || request('course_id'))
                    <a href="{{ route('admin.course-intakes.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>
        </div>
        <div class="table-responsive" style="min-height: 280px;">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>University</th>
                        <th>Course</th>
                        <th>Intake</th>
                        <th>Application Start</th>
                        <th>Deadline</th>
                        <th>Class Start</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($intakes as $intake)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $intake->course->university->name ?? 'N/A' }}</td>
                            <td>{{ $intake->course->name ?? 'N/A' }}</td>
                            <td>{{ $intake->intake_name ?? 'N/A' }}</td>
                            <td>{{ $intake->application_start_date?->format('d M Y') ?? 'N/A' }}</td>
                            <td>{{ $intake->application_deadline?->format('d M Y') ?? 'N/A' }}</td>
                            <td>{{ $intake->class_start_date?->format('d M Y') ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $intake->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $intake->status ? 'Active' : 'Inactive' }}
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
                                            <button type="button" @click="toggle(); openEditModal({
                                                id: {{ $intake->id }},
                                                course_id: '{{ $intake->course_id }}',
                                                intake_name: @js($intake->intake_name),
                                                application_start_date: '{{ $intake->application_start_date?->format('Y-m-d') ?? '' }}',
                                                application_deadline: '{{ $intake->application_deadline?->format('Y-m-d') ?? '' }}',
                                                class_start_date: '{{ $intake->class_start_date?->format('Y-m-d') ?? '' }}',
                                                status: {{ $intake->status ? 1 : 0 }}
                                            })" class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.course-intakes.destroy', $intake->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this intake?');" class="m-0">
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
                            <td colspan="9" class="text-center py-6">No intakes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $intakes->links() }}
        </div>
    </div>

    {{-- Wider Modal for Course Intake (max-w-2xl) --}}
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

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Course Intake' : 'Add Course Intake'"></h5>
                        <button type="button" @click="modalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <input type="hidden" name="_method" x-show="isEdit" value="PUT">

                        <div class="p-6 space-y-4">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group md:col-span-2">
                                    <label>Course <span class="text-danger">*</span></label>
                                    <select name="course_id" class="form-select" x-model="form.course_id" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">
                                                {{ $course->university->name ?? '' }} - {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label>Intake Name <span class="text-danger">*</span></label>
                                    <input type="text" name="intake_name" class="form-input" x-model="form.intake_name" required placeholder="e.g. September 2026">
                                </div>

                                <div class="form-group">
                                    <label>Application Start Date (Optional)</label>
                                    <input type="date" name="application_start_date" class="form-input" x-model="form.application_start_date">
                                </div>

                                <div class="form-group">
                                    <label>Application Deadline (Optional)</label>
                                    <input type="date" name="application_deadline" class="form-input" x-model="form.application_deadline">
                                </div>

                                <div class="form-group">
                                    <label>Class Start Date (Optional)</label>
                                    <input type="date" name="class_start_date" class="form-input" x-model="form.class_start_date">
                                </div>

                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" x-model="form.status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] px-6 py-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-primary px-8" x-text="isEdit ? 'Update Course Intake' : 'Save Course Intake'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function intakeModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
        actionUrl: '{{ route('admin.course-intakes.store') }}',
        form: {
            course_id: @js(old('course_id', '')),
            intake_name: @js(old('intake_name', '')),
            application_start_date: @js(old('application_start_date', '')),
            application_deadline: @js(old('application_deadline', '')),
            class_start_date: @js(old('class_start_date', '')),
            status: @js(old('status', 1)),
        },
        openCreateModal() {
            this.isEdit = false;
            this.actionUrl = '{{ route('admin.course-intakes.store') }}';
            this.form = {
                course_id: '',
                intake_name: '',
                application_start_date: '',
                application_deadline: '',
                class_start_date: '',
                status: 1
            };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.actionUrl = '{{ url('dashboard/course-intakes') }}/' + item.id;
            this.form = {
                course_id: item.course_id,
                intake_name: item.intake_name,
                application_start_date: item.application_start_date,
                application_deadline: item.application_deadline,
                class_start_date: item.class_start_date,
                status: item.status
            };
            this.modalOpen = true;
        }
    };
}
</script>

@endsection
