@extends('admin.layouts.master')

@section('title', 'Edit User')

@section('content')
<div x-data="{ suspendModalOpen: false, showPlainPwd: false }">

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">Edit User: {{ $editUser->name }}</h2>
            <p class="text-xs text-white-dark mt-1">Update profile information, security credentials, and role assignments.</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Main Form (2 cols) --}}
        <div class="lg:col-span-2">
            <div class="panel">
                <form action="{{ route('tyro-dashboard.users.update', $editUser->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Section 1: Identification --}}
                    <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                        <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">1. Identification</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="name" name="name"
                                       class="form-input text-xs @error('name') border-danger @enderror"
                                       value="{{ old('name', $editUser->name) }}" required>
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
                                       value="{{ old('username', $editUser->username) }}" required>
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
                                       value="{{ old('email', $editUser->email) }}" required>
                                @error('email')
                                    <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Password & Designation --}}
                    <div class="border-b border-[#e0e6ed] pb-5 mb-5 dark:border-[#1b2e4b]">
                        <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">2. Password &amp; Level</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    New Password <span class="text-white-dark font-normal">(leave blank to keep)</span>
                                </label>
                                <input type="password" id="password" name="password"
                                       class="form-input text-xs @error('password') border-danger @enderror"
                                       placeholder="••••••••">
                                @error('password')
                                    <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Confirm New Password
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-input text-xs"
                                       placeholder="••••••••">
                            </div>

                            <div>
                                <label for="designation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Designation Level
                                </label>
                                <select id="designation" name="designation" class="form-select text-xs @error('designation') border-danger @enderror">
                                    <option value="">None</option>
                                    <option value="senior" {{ old('designation', $editUser->designation) == 'senior' ? 'selected' : '' }}>Senior (can claim commission)</option>
                                    <option value="junior" {{ old('designation', $editUser->designation) == 'junior' ? 'selected' : '' }}>Junior (cannot claim commission)</option>
                                </select>
                                @error('designation')
                                    <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Roles --}}
                    <div class="pb-3">
                        <h5 class="text-sm font-bold uppercase tracking-wider text-primary mb-3">3. Assign Roles</h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($roles as $role)
                                <label class="relative flex items-start gap-3 rounded-xl border border-[#e0e6ed] p-3 hover:border-primary transition cursor-pointer dark:border-[#1b2e4b] dark:bg-[#121e32]">
                                    <input type="checkbox"
                                           name="roles[]"
                                           value="{{ $role->id }}"
                                           class="form-checkbox text-primary mt-0.5"
                                           {{ in_array($role->id, old('roles', $editUser->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
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

                    {{-- Save Button --}}
                    <div class="flex items-center gap-3 border-t border-[#e0e6ed] pt-5 mt-5 dark:border-[#1b2e4b]">
                        <button type="submit" class="btn btn-primary gap-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            <span>Save Changes</span>
                        </button>
                        <a href="{{ route('tyro-dashboard.users.index') }}" class="btn btn-outline-danger">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column: Status & Security Panels (1 col) --}}
        <div class="space-y-6">

            {{-- Account Status Panel --}}
            <div class="panel">
                <h5 class="text-sm font-bold uppercase tracking-wider text-dark dark:text-white-light border-b border-[#e0e6ed] pb-3 mb-4 dark:border-[#1b2e4b]">
                    Account Status
                </h5>

                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-bold text-base uppercase dark:bg-primary/20">
                        {{ strtoupper(substr($editUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-dark dark:text-white-light">{{ $editUser->name }}</div>
                        <div class="text-xs text-white-dark">Member since {{ $editUser->created_at ? $editUser->created_at->format('M d, Y') : '—' }}</div>
                    </div>
                </div>

                <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100 dark:bg-dark/40 dark:border-dark space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-white-dark font-medium">Status</span>
                        @if(method_exists($editUser, 'isSuspended') && $editUser->isSuspended())
                            <span class="badge bg-danger/10 text-danger border border-danger/20 font-bold px-2.5 py-0.5 rounded">Suspended</span>
                        @else
                            <span class="badge bg-success/10 text-success border border-success/20 font-bold px-2.5 py-0.5 rounded">Active</span>
                        @endif
                    </div>

                    @if(method_exists($editUser, 'isSuspended') && $editUser->isSuspended() && method_exists($editUser, 'getSuspensionReason') && $editUser->getSuspensionReason())
                        <div class="pt-2 border-t border-slate-200 dark:border-dark">
                            <span class="text-white-dark block text-[11px]">Suspension Reason:</span>
                            <p class="font-semibold text-danger mt-0.5">{{ $editUser->getSuspensionReason() }}</p>
                        </div>
                    @endif
                </div>

                {{-- Plain Password Viewer (Super Admin) --}}
                @if(auth()->user()->hasAnyRole(['admin', 'super-admin', 'superadmin']) && !empty($editUser->plain_password))
                    <div class="mt-4 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Plain Credentials</span>
                            <span class="badge bg-warning/10 text-warning border border-warning/20 text-[10px] font-bold px-2 py-0.5 rounded">Super Admin</span>
                        </div>
                        <div class="relative">
                            <input :type="showPlainPwd ? 'text' : 'password'"
                                   value="{{ $editUser->plain_password }}"
                                   readonly
                                   class="form-input text-xs font-mono pr-10 bg-slate-50 dark:bg-dark/60 font-bold text-primary">
                            <button type="button"
                                    @click="showPlainPwd = !showPlainPwd"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition"
                                    title="Toggle visibility">
                                <svg x-show="!showPlainPwd" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg x-show="showPlainPwd" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Suspend / Unsuspend Buttons --}}
                <div class="mt-4 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                    @if(method_exists($editUser, 'isSuspended') && $editUser->isSuspended())
                        <form action="{{ route('tyro-dashboard.users.unsuspend', $editUser->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-full gap-2 text-xs font-bold py-2" onclick="return confirm('Unsuspend {{ addslashes($editUser->name) }}?');">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Unsuspend User</span>
                            </button>
                        </form>
                    @elseif($editUser->id !== ($user->id ?? auth()->id()))
                        <button type="button" @click="suspendModalOpen = true" class="btn btn-warning w-full gap-2 text-xs font-bold py-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <span>Suspend User</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Danger Zone Panel --}}
            @if($editUser->id !== ($user->id ?? auth()->id()))
                <div class="panel border-danger/30 bg-danger/5 dark:bg-danger-dark-light/10">
                    <h5 class="text-sm font-bold uppercase tracking-wider text-danger mb-2">Danger Zone</h5>
                    <p class="text-xs text-white-dark mb-4">Deleting this user account permanently removes their access credentials.</p>
                    <form action="{{ route('tyro-dashboard.users.destroy', $editUser->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to permanently delete {{ addslashes($editUser->name) }}? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-full gap-2 text-xs font-bold py-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            <span>Delete User Account</span>
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>

    {{-- Suspend Modal conforming to AGENTS.md Rule 1 --}}
    <template x-teleport="#modal-portal">
        <div x-cloak x-show="suspendModalOpen" class="modal-overlay-full" style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="suspendModalOpen = false"
                     x-show="suspendModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2 text-danger">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>Suspend {{ $editUser->name }}</span>
                        </h5>
                        <button type="button" @click="suspendModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('tyro-dashboard.users.suspend', $editUser->id) }}" method="POST">
                        @csrf
                        <div class="p-5 space-y-4">
                            <p class="text-xs text-white-dark leading-relaxed">
                                You are about to suspend <strong class="text-dark dark:text-white font-bold">{{ $editUser->name }}</strong>. This will revoke all their active sessions and immediately prevent login.
                            </p>
                            <div>
                                <label for="edit_suspend_reason" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Reason for suspension <span class="text-white-dark font-normal">(optional)</span>
                                </label>
                                <textarea id="edit_suspend_reason" name="reason" rows="3" class="form-textarea text-xs w-full" placeholder="Enter reason for suspension..."></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="suspendModalOpen = false" class="btn btn-outline-danger btn-sm">Cancel</button>
                            <button type="submit" class="btn btn-danger btn-sm">Confirm Suspension</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection
