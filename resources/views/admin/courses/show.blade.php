@extends('admin.layouts.master')

@section('title', 'Course Details')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Course Details</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to List
            </a>
            @can('edit-course')
                <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-primary gap-2">Edit Course</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 pt-5 lg:grid-cols-3">
        <!-- Main Details -->
        <div class="panel lg:col-span-2">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">Information</h5>
            </div>
            <div class="space-y-4">
                <div class="flex flex-col md:flex-row md:items-center">
                    <span class="w-full md:w-1/3 font-bold text-white-dark">Course Name:</span>
                    <span class="w-full md:w-2/3 text-lg font-semibold text-primary">{{ $course->name }}</span>
                </div>
                <hr class="border-white-light dark:border-[#1b2e4b]">
                <div class="flex flex-col md:flex-row md:items-center">
                    <span class="w-full md:w-1/3 font-bold text-white-dark">University:</span>
                    <span class="w-full md:w-2/3">{{ $course->university->name ?? 'N/A' }}</span>
                </div>
                <hr class="border-white-light dark:border-[#1b2e4b]">
                <div class="flex flex-col md:flex-row">
                    <span class="w-full md:w-1/3 font-bold text-white-dark">Description:</span>
                    <div class="w-full md:w-2/3 text-white-dark leading-relaxed quill-content">
                        {!! $course->description ?? 'No description available.' !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="panel">
            <div class="mb-5">
                <h5 class="text-lg font-semibold dark:text-white-light">Quick Stats</h5>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-white-dark">Degree Level:</span>
                    <span class="badge bg-info-light text-info">{{ $course->degree_level ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-white-dark">Duration:</span>
                    <span>{{ $course->duration ? $course->duration . ' Months' : 'N/A' }}</span>
                </div>
                <!-- <div class="flex items-center justify-between">
                    <span class="font-bold text-white-dark">Tuition Fee:</span>
                    <span class="font-bold text-success">{{ $course->tuition_fee ? number_format($course->tuition_fee, 2) : 'N/A' }}</span>
                </div> -->
                <div class="flex items-center justify-between">
                    <span class="font-bold text-white-dark">Status:</span>
                    <span class="badge {{ $course->status ? 'bg-success' : 'bg-danger' }}">
                        {{ $course->status ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <hr class="border-white-light dark:border-[#1b2e4b]">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-white-dark">Added On:</span>
                    <span>{{ $course->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
