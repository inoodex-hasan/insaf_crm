@extends('admin.layouts.master')

@section('title', 'Edit VFS Status')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Edit VFS Status & Notes</h2>
        <a href="{{ route('admin.vfs-checklist.overview') }}" class="btn btn-secondary gap-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Overview
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 pt-5 lg:grid-cols-2">
        <div class="panel">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light uppercase">Student Information</h5>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-white-dark font-bold">Application ID:</span>
                    <span class="font-semibold">{{ $application->application_id }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-white-dark font-bold">Student Name:</span>
                    <span>{{ $application->student->first_name }} {{ $application->student->last_name }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-white-dark font-bold">Passport Number:</span>
                    <span>{{ $application->student->passport_number ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-white-dark font-bold">University:</span>
                    <span>{{ $application->university?->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-white-dark font-bold">VFS Appointment Date:</span>
                    <span class="text-primary font-bold">{{ $application->vfs_appointment_date ? $application->vfs_appointment_date->format('M d, Y') : 'Not Set' }}</span>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="mb-5">
                <h5 class="text-lg font-semibold dark:text-white-light uppercase">Update VFS Result</h5>
            </div>
            <form action="{{ route('admin.vfs-checklist.update-vfs-result', $application->id) }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="font-bold mb-2 block">VFS Result <span class="text-danger">*</span></label>
                        <select name="vfs_result" class="form-select" required>
                            <option value="submitted" {{ $application->vfs_result == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="not_submitted" {{ $application->vfs_result == 'not_submitted' ? 'selected' : '' }}>Not Submitted</option>
                            <option value="not_attended" {{ $application->vfs_result == 'not_attended' ? 'selected' : '' }}>Not Attended</option>
                            <option value="rejected" {{ $application->vfs_result == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="approved" {{ $application->vfs_result == 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold mb-2 block">VFS Notes / Rejection Reason</label>
                        <textarea name="vfs_note" class="form-textarea h-32" placeholder="Provide details about the VFS outcome...">{{ old('vfs_note', $application->vfs_note) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-5">
                        <button type="submit" class="btn btn-primary px-10">Save Updates</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
