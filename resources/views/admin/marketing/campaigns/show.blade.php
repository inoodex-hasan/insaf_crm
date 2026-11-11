@extends('admin.layouts.master')

@section('title', 'Campaign Assets: ' . $campaign->name)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-semibold uppercase">Campaign Dashboard</h2>
            <span class="badge {{ $campaign->boosting_status === 'on' ? 'badge-outline-primary' : 'badge-outline-dark' }} uppercase text-[10px]">
                Boosting {{ $campaign->boosting_status }}
            </span>
        </div>
        <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary gap-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Campaigns
        </a>
    </div>

    <!-- Campaign Header Info -->
    <div class="panel mt-6 bg-gradient-to-r from-primary/5 to-transparent border-l-4 border-l-primary">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-primary">{{ $campaign->name }}</h1>
                <p class="text-white-dark mt-1 italic italic">{{ $campaign->notes ?: 'No additional notes provided.' }}</p>
            </div>
            <div class="flex flex-col items-end text-xs text-white-dark">
                <span>Created by: <b>{{ $campaign->creator->name ?? 'N/A' }}</b></span>
                <span>Created on: <b>{{ $campaign->created_at->format('M d, Y') }}</b></span>
            </div>
        </div>
    </div>

    <!-- Campaign Details Panel -->
    <div class="panel mt-6">
        <h3 class="text-lg font-bold mb-4">Campaign Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-white-dark mb-1">Start Date</p>
                <p class="font-semibold">{{ $campaign->start_date?->format('M d, Y') ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-sm text-white-dark mb-1">End Date</p>
                <p class="font-semibold {{ $campaign->end_date && $campaign->end_date < now() ? 'text-danger' : '' }}">{{ $campaign->end_date?->format('M d, Y') ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-sm text-white-dark mb-1">Boosting Status</p>
                <span class="badge {{ $campaign->boosting_status === 'on' ? 'badge-outline-primary' : 'badge-outline-dark' }} uppercase">{{ $campaign->boosting_status }}</span>
            </div>
            <div>
                <p class="text-sm text-white-dark mb-1">Created By</p>
                <p class="font-semibold">{{ $campaign->creator->name ?? 'System' }}</p>
            </div>
        </div>
        @if($campaign->notes)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-white-dark mb-1">Notes</p>
                <p class="italic">{{ $campaign->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <a href="{{ route('admin.marketing.videos.index') }}" class="panel flex items-center justify-between hover:bg-info/5 transition-colors">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-info/10 flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-info">
                        <polygon points="23 7 16 12 23 17 23 7"></polygon>
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold">Videos</h4>
                    <p class="text-xs text-white-dark">Manage video assets</p>
                </div>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"></path>
            </svg>
        </a>
        <a href="{{ route('admin.marketing.posters.index') }}" class="panel flex items-center justify-between hover:bg-success/5 transition-colors">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-success/10 flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-success">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold">Posters</h4>
                    <p class="text-xs text-white-dark">Manage poster assets</p>
                </div>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"></path>
            </svg>
        </a>
    </div>
@endsection
