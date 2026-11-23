@extends('admin.layouts.master')

@section('title', 'Students')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Students</h2>
        @can('*consultant')
            <div class="flex w-full flex-wrap items-center justify-end gap-4 sm:w-auto">
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Student
                </a>
            </div>
        @endcan
    </div>

    <div class="panel mt-6">
        <!-- Toolbar: KPI summary & Search -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalCount ?? $students->total() }} Students
                </span>
                <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Enrolled: {{ $enrolledCount ?? 0 }}
                </span>
            </div>

            <form action="{{ route('admin.students.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                <select name="stage" class="form-select text-xs" style="height: 36px; width: 125px; border-radius: 6px;" onchange="this.form.submit()">
                    <option value="">All Stages</option>
                    @foreach (['lead', 'counseling', 'payment', 'application', 'offer', 'visa', 'enrolled'] as $stage)
                        <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>
                            {{ ucfirst($stage) }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="form-select text-xs" style="height: 36px; width: 125px; border-radius: 6px;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['lead', 'counseling', 'payment', 'application', 'offer', 'visa', 'enrolled'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>

                <select name="collected_by" class="form-select text-xs" style="height: 36px; width: 145px; border-radius: 6px;" onchange="this.form.submit()">
                    <option value="">Collected By</option>
                    @foreach ($collectors ?? [] as $collector)
                        <option value="{{ $collector->id }}" {{ (request('collected_by') == $collector->id || request('created_by') == $collector->id) ? 'selected' : '' }}>
                            {{ $collector->name }}
                        </option>
                    @endforeach
                </select>

                <div style="position: relative; width: 220px;">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search name, phone, email..."
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

                @if(request('search') || request('stage') || request('status') || request('collected_by') || request('created_by'))
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>
        </div>

        <div class="datatable">
            <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Father's Name</th>
                            <th>Phone</th>
                            <!-- <th>Stage</th>
                                                                                                    <th>Status</th> -->
                            <th>Address</th>
                            <th>Collected By</th>
                            <!-- <th>Assigned By</th> -->
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>
                                    <div class="font-semibold">
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="text-primary hover:underline">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-white-dark">{{ $student->email ?? 'No Email' }}</div>
                                </td>
                                <td>{{ $student->father_name }}</td>
                                <td>
                                    @if ($student->phone)
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$student->phone);
                                            if (str_starts_with($cleanPhone, '01') && strlen($cleanPhone) === 11) {
                                                $waNumber = '88' . $cleanPhone;
                                            } elseif (str_starts_with($cleanPhone, '8801') && strlen($cleanPhone) === 13) {
                                                $waNumber = $cleanPhone;
                                            } else {
                                                $waNumber = $cleanPhone;
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 font-medium text-primary hover:text-success hover:underline"
                                            title="Chat on WhatsApp">
                                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            <span>{{ $student->phone }}</span>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <!-- <td>
                                                                                                                                    <span class="badge badge-outline-primary capitalize">{{ $student->current_stage }}</span>
                                                                                                                                </td>
                                                                                                                                <td>
                                                                                                                                    <span class="badge badge-outline-primary capitalize">{{ $student->current_status }}</span>
                                                                                                                                </td> -->
                                <td>{{ \Illuminate\Support\Str::limit($student->address ?? '', 30) }}</td>
                                <td>
                                    @if ($student->creator)
                                        <span class="badge badge-outline-primary">{{ $student->creator->name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <!-- <td>
                                                    <span
                                                        class="badge badge-outline-secondary">{{ $student->marketingAssignee->name ?? '-' }}</span>
                                                </td> -->
                                <td class="text-center">
                                    <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                            <li>
                                                <a href="{{ route('admin.students.show', $student->id) }}" @click="toggle">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    View
                                                </a>
                                            </li>
                                            @can('*consultant')
                                                <li>
                                                    <a href="{{ route('admin.students.edit', $student->id) }}" @click="toggle">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                                        onsubmit="return confirm('Delete this student?');" class="m-0">
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
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
@endsection