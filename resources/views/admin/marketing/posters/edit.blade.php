@extends('admin.layouts.master')

@section('title', 'Edit Poster')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Edit Poster</h2>
        <a href="{{ route('admin.marketing.posters.index') }}" class="btn btn-secondary gap-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Posters
        </a>
    </div>

    <div class="panel mt-6">
        <form action="{{ route('admin.marketing.posters.update', $poster) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-sm font-bold">Poster Name</label>
                <input type="text" name="poster_name" value="{{ old('poster_name', $poster->poster_name) }}" class="form-input" required />
                @error('poster_name')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="text-sm font-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ old('status', $poster->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="not_ready" {{ old('status', $poster->status) == 'not_ready' ? 'selected' : '' }}>Not Ready</option>
                    <option value="designing" {{ old('status', $poster->status) == 'designing' ? 'selected' : '' }}>Designing</option>
                    <option value="ready" {{ old('status', $poster->status) == 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="uploaded" {{ old('status', $poster->status) == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
                </select>
                @error('status')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.marketing.posters.index') }}" class="btn btn-outline-danger">Cancel</a>
            </div>
        </form>
    </div>
@endsection
