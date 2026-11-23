@extends('admin.layouts.master')

@section('title', 'VFS Checklist')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">VFS Checklist</h2>
    </div>

    <div class="panel mt-6">
        <!-- Toolbar: KPI summary & Search -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalApplications ?? $applications->total() }} Applications
                </span>
                <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    VFS Scheduled: {{ $vfsScheduled ?? 0 }}
                </span>
            </div>

            <form action="{{ route('admin.vfs-checklist.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                <div style="position: relative; width: 260px;">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search ID, student name..."
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

                @if(request('search'))
                    <a href="{{ route('admin.vfs-checklist.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive" style="min-height: 280px;">
            <table class="table-hover">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Student Name</th>
                        <th>University</th>
                        <th class="text-center">Checked Items</th>
                        <th class="text-center">Total Items</th>
                        <th class="text-center">Progress</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $activeTemplateNames = \App\Models\VfsChecklistTemplate::where('is_active', true)->pluck('item_name')->toArray();
                    @endphp
                    @forelse($applications as $app)
                        @php
                            $activeItems = $app->vfsChecklist->whereIn('checklist_item', $activeTemplateNames);
                            $totalItems = $activeItems->count();
                            $checkedItems = $activeItems->where('is_checked', true)->count();
                            $progress = $totalItems > 0 ? round(($checkedItems / $totalItems) * 100, 0) : 0;
                        @endphp
                        <tr>
                            <td class="font-bold text-primary">{{ $app->application_id }}</td>
                            <td>
                                @if($app->student)
                                    <a href="{{ route('admin.students.show', $app->student->id) }}" class="font-semibold text-primary hover:underline">
                                        {{ $app->student->first_name }} {{ $app->student->last_name }}
                                    </a>
                                @else
                                    <span class="text-danger italic">N/A</span>
                                @endif
                            </td>
                            <td>{{ $app->university?->name ?? '-' }}</td>
                            <td class="text-center">{{ $checkedItems }}</td>
                            <td class="text-center">{{ $totalItems }}</td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="bg-{{ $progress == 100 ? 'success' : 'primary' }} h-2.5 rounded-full"
                                            style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold">{{ $progress }}%</span>
                                </div>
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
                                            <a href="{{ route('admin.vfs-checklist.show', $app) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 11 12 14 22 4"></polyline>
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                </svg>
                                                View Checklist
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.applications.show', $app) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                View Application
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-400 py-12">
                                @if(request('search'))
                                    No applications found matching "{{ request('search') }}".
                                @else
                                    No applications found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $applications->links() }}
        </div>
    </div>
@endsection