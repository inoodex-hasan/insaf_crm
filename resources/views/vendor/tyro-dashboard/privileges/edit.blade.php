@extends('admin.layouts.master')

@section('title', 'Edit Privilege')

@section('content')
<div>

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">Edit Privilege: {{ $privilege->name }}</h2>
            <p class="text-xs text-white-dark mt-1">Update privilege definitions and associated role permissions.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('tyro-dashboard.privileges.destroy', $privilege->id) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this privilege? It will be automatically removed from all assigned roles.');" class="m-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger gap-1.5">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    <span>Delete Privilege</span>
                </button>
            </form>
            <a href="{{ route('tyro-dashboard.privileges.index') }}" class="btn btn-secondary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back to Privileges</span>
            </a>
        </div>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="mb-5 rounded-lg border border-danger/20 bg-danger/5 p-4 text-danger dark:border-danger/30">
            <div class="flex items-center gap-2 font-bold text-xs uppercase mb-1">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc pl-5 text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Panel --}}
    <div class="panel mt-6">
        <form action="{{ route('tyro-dashboard.privileges.update', $privilege->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Section 1: Identification --}}
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">1. Capability Details</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Privilege Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               class="form-input text-xs @error('name') border-danger @enderror"
                               value="{{ old('name', $privilege->name) }}" required>
                        @error('name')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Slug
                        </label>
                        <input type="text" id="slug" name="slug"
                               class="form-input text-xs @error('slug') border-danger @enderror"
                               value="{{ old('slug', $privilege->slug) }}">
                        @error('slug')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Description <span class="text-white-dark font-normal">(optional)</span>
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-textarea text-xs @error('description') border-danger @enderror">{{ old('description', $privilege->description) }}</textarea>
                        @error('description')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Assign to Roles --}}
            <div class="pb-3">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">2. Assign to Roles</h5>
                <p class="text-xs text-white-dark mb-4">Select the roles that should inherit this privilege.</p>

                @if($roles->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($roles as $role)
                            <label class="relative flex items-start gap-3 rounded-xl border border-[#e0e6ed] p-3.5 hover:border-primary transition cursor-pointer dark:border-[#1b2e4b] dark:bg-[#121e32]">
                                <input type="checkbox"
                                       name="roles[]"
                                       value="{{ $role->id }}"
                                       class="form-checkbox text-primary mt-0.5"
                                       {{ in_array($role->id, old('roles', $privilege->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <div>
                                    <div class="text-xs font-bold text-dark dark:text-white-light">{{ $role->name }}</div>
                                    <code class="text-[11px] text-primary font-mono block mt-0.5">{{ $role->slug }}</code>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-info/10 p-4 text-info border border-info/20 text-xs">
                        No roles available. <a href="{{ route('tyro-dashboard.roles.create') }}" class="font-bold underline">Create one first</a>.
                    </div>
                @endif

                @error('roles')
                    <span class="text-danger text-xs mt-2 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center gap-3 border-t border-[#e0e6ed] pt-5 mt-5 dark:border-[#1b2e4b]">
                <button type="submit" class="btn btn-primary gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Save Changes</span>
                </button>
                <a href="{{ route('tyro-dashboard.privileges.index') }}" class="btn btn-outline-danger">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
