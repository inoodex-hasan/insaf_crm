@extends('admin.layouts.master')

@section('title', 'Create Role')

@section('content')
<div>

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">Create Role</h2>
            <p class="text-xs text-white-dark mt-1">Define an authorization role and assign system privileges.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tyro-dashboard.roles.index') }}" class="btn btn-secondary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back to Roles</span>
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
        <form action="{{ route('tyro-dashboard.roles.store') }}" method="POST">
            @csrf

            {{-- Section 1: Role Identification --}}
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">1. Role Identification</h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               class="form-input text-xs @error('name') border-danger @enderror"
                               value="{{ old('name') }}" required placeholder="e.g. Admission Officer">
                        @error('name')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Slug <span class="text-white-dark font-normal">(auto-generated if empty)</span>
                        </label>
                        <input type="text" id="slug" name="slug"
                               class="form-input text-xs @error('slug') border-danger @enderror"
                               value="{{ old('slug') }}" placeholder="e.g. admission-officer">
                        @error('slug')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Status
                        </label>
                        <label class="inline-flex items-center gap-2 mt-2 cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                   class="form-checkbox text-primary"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="text-xs font-bold text-dark dark:text-white-light">Role is Active</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Section 2: Privilege Assignments --}}
            <div class="pb-3">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">2. Assign Privileges</h5>
                <p class="text-xs text-white-dark mb-4">Select the system privileges granted to users holding this role.</p>

                @if($privileges->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($privileges as $privilege)
                            <label class="relative flex items-start gap-3 rounded-xl border border-[#e0e6ed] p-3.5 hover:border-primary transition cursor-pointer dark:border-[#1b2e4b] dark:bg-[#121e32]">
                                <input type="checkbox"
                                       name="privileges[]"
                                       value="{{ $privilege->id }}"
                                       class="form-checkbox text-primary mt-0.5"
                                       {{ in_array($privilege->id, old('privileges', [])) ? 'checked' : '' }}>
                                <div>
                                    <div class="text-xs font-bold text-dark dark:text-white-light">{{ $privilege->name }}</div>
                                    <code class="text-[11px] text-primary font-mono block mt-0.5">{{ $privilege->slug }}</code>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-info/10 p-4 text-info border border-info/20 text-xs">
                        No privileges available yet. <a href="{{ route('tyro-dashboard.privileges.create') }}" class="font-bold underline">Create one first</a>.
                    </div>
                @endif

                @error('privileges')
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
                    <span>Create Role</span>
                </button>
                <a href="{{ route('tyro-dashboard.roles.index') }}" class="btn btn-outline-danger">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
