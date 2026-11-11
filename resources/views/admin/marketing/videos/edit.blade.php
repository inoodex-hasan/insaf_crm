@extends('admin.layouts.master')

@section('title', 'Edit Video')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Edit Video</h2>
        <a href="{{ route('admin.marketing.videos.index') }}" class="btn btn-secondary gap-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Videos
        </a>
    </div>

    <div class="panel mt-6">
        <form action="{{ route('admin.marketing.videos.update', $video) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-sm font-bold">Video Name</label>
                <input type="text" name="video_name" value="{{ old('video_name', $video->video_name) }}" class="form-input" required />
                @error('video_name')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="text-sm font-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ old('status', $video->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="not_edited" {{ old('status', $video->status) == 'not_edited' ? 'selected' : '' }}>Not Edited</option>
                    <option value="editing" {{ old('status', $video->status) == 'editing' ? 'selected' : '' }}>Editing</option>
                    <option value="ready" {{ old('status', $video->status) == 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="uploaded" {{ old('status', $video->status) == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
                </select>
                @error('status')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.marketing.videos.index') }}" class="btn btn-outline-danger">Cancel</a>
            </div>
        </form>
    </div>
@endsection
