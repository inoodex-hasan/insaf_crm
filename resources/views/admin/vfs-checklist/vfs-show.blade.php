@extends('admin.layouts.master')

@section('title', 'VFS Details')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">VFS Record Details</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.vfs-checklist.overview') }}" class="btn btn-secondary gap-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Overview
            </a>
            <a href="{{ route('admin.vfs-checklist.vfs-edit', $application->id) }}" class="btn btn-primary">Edit Record</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 pt-5 lg:grid-cols-3">
        <!-- Main Info -->
        <div class="panel lg:col-span-2">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light uppercase">Application Information</h5>
                <span class="badge {{ $application->vfs_result == 'passed' ? 'bg-success' : ($application->vfs_result == 'rejected' ? 'bg-danger' : 'bg-warning') }} uppercase px-4">
                    {{ $application->vfs_result }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="text-white-dark font-bold text-xs uppercase">Student Name</label>
                        <p class="text-lg font-semibold">{{ $application->student->first_name }} {{ $application->student->last_name }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark font-bold text-xs uppercase">Passport Number</label>
                        <p class="font-medium">{{ $application->student->passport_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark font-bold text-xs uppercase">University</label>
                        <p class="font-medium">{{ $application->university?->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-white-dark font-bold text-xs uppercase">Application ID</label>
                        <p class="font-medium text-primary">{{ $application->application_id }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark font-bold text-xs uppercase">VFS Appointment Date</label>
                        <p class="font-medium">{{ $application->vfs_appointment_date ? $application->vfs_appointment_date->format('M d, Y') : 'Not Set' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark font-bold text-xs uppercase">Country</label>
                        <p class="font-medium">{{ $application->university?->country?->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t pt-6">
                <h5 class="text-md font-bold text-white-dark uppercase mb-2">VFS Result Note</h5>
                <div class="bg-gray-50 dark:bg-black/20 p-4 rounded-lg min-h-[100px] border border-dashed border-gray-300">
                    {!! nl2br(e($application->vfs_note ?? 'No notes available for this record.')) !!}
                </div>
            </div>
        </div>

        <!-- History -->
        <div class="panel">
            <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-5">History Tracking</h5>
            @if($application->history->count() > 0)
                <div class="space-y-4">
                    @foreach($application->history as $prev)
                        <div class="border-b border-dashed pb-4 last:border-0 last:pb-0">
                            <div class="flex justify-between items-start">
                                <span class="text-xs font-bold text-white-dark">{{ $prev->vfs_appointment_date?->format('M d, Y') }}</span>
                                <span class="badge badge-outline-{{ $prev->vfs_result == 'passed' ? 'success' : 'danger' }} text-[10px] uppercase">
                                    {{ $prev->vfs_result }}
                                </span>
                            </div>
                            <p class="text-sm font-medium mt-1">{{ $prev->university?->name }}</p>
                            @if($prev->vfs_note)
                                <p class="text-xs text-gray-500 italic mt-1 bg-gray-100 p-1 rounded">"{{ Illuminate\Support\Str::limit($prev->vfs_note, 50) }}"</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10">
                    <p class="text-white-dark italic">No previous VFS attempts found for this student.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
