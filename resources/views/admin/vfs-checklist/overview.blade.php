@extends('admin.layouts.master')

@section('title', 'VFS Overview')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">VFS Overview & Tracking</h2>
    </div>

    <div class="panel mt-6">
        <!-- Filters -->
        <form action="{{ route('admin.vfs-checklist.overview') }}" method="GET" class="mb-6">
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Search Application ID, Student or Passport..."
                        value="{{ request('search') }}" class="form-input w-full" />
                </div>
                
                <div class="w-48">
                    <select name="vfs_result" class="form-select w-full">
                        <option value="">All Status</option>
                        <option value="submitted" {{ request('vfs_result') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="not_submitted" {{ request('vfs_result') == 'not_submitted' ? 'selected' : '' }}>Not Submitted</option>
                        <option value="not_attended" {{ request('vfs_result') == 'not_attended' ? 'selected' : '' }}>Not Attended</option>
                        <option value="rejected" {{ request('vfs_result') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="approved" {{ request('vfs_result') == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary px-6">Filter</button>
                    @if(request()->hasAny(['search', 'vfs_result']))
                        <a href="{{ route('admin.vfs-checklist.overview') }}" class="btn btn-outline-danger">Clear</a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="datatable">
            <div class="overflow-x-auto">
                <table class="table-hover w-full table-auto">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Student Name</th>
                        <th>Passport</th>
                        <th>University</th>
                        <th class="text-center">VFS Date</th>
                        <!-- <th class="text-center">History</th> -->
                        <th class="text-center">VFS Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td class="font-semibold">
                                    {{ $app->application_id }}
                                    <div class="text-[10px] text-info font-bold uppercase mt-1">Attempt #{{ $app->total_vfs_attempts }}</div>
                                </td>
                            <td>
                                <div class="font-medium">{{ $app->student->first_name }} {{ $app->student->last_name }}</div>
                                <div class="text-xs text-white-dark">{{ $app->student->email }}</div>
                            </td>
                            <td>{{ $app->student->passport_number ?? 'N/A' }}</td>
                            <td>
                                <div class="font-medium">{{ $app->university?->name ?? 'N/A' }}</div>
                                <div class="text-xs text-info">{{ $app->university?->country?->name }}</div>
                            </td>
                            <td class="py-3 px-2 text-center">
                                @php
                                    $vfsDate = $app->vfs_appointment_date ? \Carbon\Carbon::parse($app->vfs_appointment_date)->format('M d, Y') : 'Not Set';
                                    $isOverdue = $app->vfs_appointment_date && \Carbon\Carbon::parse($app->vfs_appointment_date)->isPast() && !in_array($app->vfs_result, ['approved', 'rejected', 'submitted']);
                                @endphp
                                <span class="{{ $isOverdue ? 'text-danger font-bold' : '' }}">
                                    {{ $vfsDate }}
                                    @if($isOverdue)
                                        <div class="text-[10px] uppercase">⚠️ Overdue</div>
                                    @endif
                                </span>
                            </td>
                            <!-- <td class="text-center">
                                @if($app->history->count() > 0)
                                    <div class="dropdown">
                                        <button type="button" class="badge badge-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                            {{ $app->history->count() }} Previous {{ Illuminate\Support\Str::plural('Attempt', $app->history->count()) }}
                                        </button>
                                        <ul class="dropdown-menu p-3 shadow-lg border-primary min-w-[250px]">
                                            <li class="font-bold border-b mb-2 pb-1">Previous Attempts</li>
                                            @foreach($app->history as $prev)
                                                <li class="text-xs mb-2 pb-2 border-b border-dashed last:border-0">
                                                    <div class="flex justify-between font-semibold">
                                                            <span>{{ $prev->vfs_appointment_date?->format('M d, Y') }}</span>
                                                            @php
                                                                $prevBadgeClass = match($prev->vfs_result) {
                                                                    'approved' => 'text-success',
                                                                    'rejected' => 'text-danger',
                                                                    'submitted' => 'text-primary',
                                                                    'not_submitted' => 'text-warning',
                                                                    'not_attended' => 'text-secondary',
                                                                    default => 'text-info',
                                                                };
                                                            @endphp
                                                            <span class="{{ $prevBadgeClass }} uppercase text-[10px]">{{ str_replace('_', ' ', $prev->vfs_result) }}</span>
                                                        </div>
                                                    <div class="text-white-dark italic">{{ $prev->university?->name }}</div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <span class="text-xs text-white-dark italic">1st Attempt</span>
                                @endif
                            </td> -->
                            <td class="text-center">
                                @php
                                    $badgeClass = match($app->vfs_result) {
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'submitted' => 'bg-primary',
                                        'not_submitted' => 'bg-warning',
                                        'not_attended' => 'bg-secondary',
                                        default => 'bg-info',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} uppercase">
                                    {{ str_replace('_', ' ', $app->vfs_result) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                        <li>
                                            <a href="{{ route('admin.vfs-checklist.vfs-show', $app->id) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                View
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.vfs-checklist.vfs-edit', $app->id) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-warning" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.vfs-checklist.show', $app->id) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 11 12 14 22 4"></polyline>
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                </svg>
                                                Checklist
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-white-dark">No VFS records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

        <div class="mt-4">
            {{ $applications->links() }}
        </div>
    </div>
@endsection
