@extends('admin.layouts.master')

@section('title', 'Create User')

@section('content')
<div>

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">Create User</h2>
            <p class="text-xs text-white-dark mt-1">Add a new staff account, set credentials, and assign system roles.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tyro-dashboard.users.index') }}" class="btn btn-secondary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back to Users</span>
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
        <form action="{{ route('tyro-dashboard.users.store') }}" method="POST">
            @csrf

            {{-- Section 1: Account Information --}}
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">1. Account Identification</h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               class="form-input text-xs @error('name') border-danger @enderror"
                               value="{{ old('name') }}" required placeholder="e.g. John Doe">
                        @error('name')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            User ID / Employee Code <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="username" name="username"
                               class="form-input text-xs @error('username') border-danger @enderror"
                               value="{{ old('username') }}" required placeholder="e.g. EMP001">
                        @error('username')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                               class="form-input text-xs @error('email') border-danger @enderror"
                               value="{{ old('email') }}" required placeholder="e.g. user@domain.com">
                        @error('email')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Security & Designation --}}
            <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">2. Credentials & Designation</h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="password" name="password"
                               class="form-input text-xs @error('password') border-danger @enderror"
                               required placeholder="••••••••">
                        @error('password')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Confirm Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-input text-xs"
                               required placeholder="••••••••">
                    </div>

                    <div>
                        <label for="designation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Designation Level
                        </label>
                        <select id="designation" name="designation" class="form-select text-xs @error('designation') border-danger @enderror">
                            <option value="">None</option>
                            <option value="senior" {{ old('designation') == 'senior' ? 'selected' : '' }}>Senior (can claim commission)</option>
                            <option value="junior" {{ old('designation') == 'junior' ? 'selected' : '' }}>Junior (cannot claim commission)</option>
                        </select>
                        @error('designation')
                            <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 3: Role Assignments --}}
            <div class="pb-3">
                <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">3. Assign Roles</h5>
                <p class="text-xs text-white-dark mb-4">Select the roles that grant permissions to this user account.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($roles as $role)
                        <label class="relative flex items-start gap-3 rounded-xl border border-[#e0e6ed] p-3.5 hover:border-primary transition cursor-pointer dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   class="form-checkbox text-primary mt-0.5"
                                   {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                            <div>
                                <div class="text-xs font-bold text-dark dark:text-white-light">{{ $role->name }}</div>
                                <code class="text-[11px] text-primary font-mono block mt-0.5">{{ $role->slug }}</code>
                            </div>
                        </label>
                    @endforeach
                </div>
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
                    <span>Create User</span>
                </button>
                <a href="{{ route('tyro-dashboard.users.index') }}" class="btn btn-outline-danger">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
