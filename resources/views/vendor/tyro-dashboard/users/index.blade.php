@extends('admin.layouts.master')

@section('title', 'Users')

@section('content')
<div x-data="usersPageHandler()">

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">Users</h2>
            <p class="text-xs text-white-dark mt-1">Manage user accounts, credentials, designations, and role assignments.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tyro-dashboard.users.create') }}" class="btn btn-primary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Add User</span>
            </a>
        </div>
    </div>

    {{-- Main Panel Container --}}
    <div class="panel mt-6">

        {{-- Filter & Search Toolbar --}}
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            
            {{-- KPI Summary Badges --}}
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $users->total() }} Users
                </span>
                <span class="badge bg-info/10 text-info border border-info/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Roles: {{ $roles->count() }} Available
                </span>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('tyro-dashboard.users.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                {{-- Role Filter --}}
                <select name="role" onchange="this.form.submit()" class="form-select text-xs" style="height: 36px; width: 150px; min-width: 150px; border-radius: 6px; flex-shrink: 0;">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->slug }}" {{ ($filters['role'] ?? '') === $role->slug ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Status Filter --}}
                <select name="status" onchange="this.form.submit()" class="form-select text-xs" style="height: 36px; width: 130px; min-width: 130px; border-radius: 6px; flex-shrink: 0;">
                    <option value="">All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                {{-- Search Box --}}
                <div style="position: relative; width: 220px; min-width: 220px; flex-shrink: 0;">
                    <input type="text"
                           name="search"
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Search users..."
                           class="form-input text-xs w-full"
                           style="height: 36px; padding-right: 36px; border-radius: 6px;" />
                    <button type="submit"
                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; cursor: pointer;"
                            class="text-gray-400 hover:text-primary"
                            title="Search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>

                {{-- Reset Button --}}
                @if(!empty($filters['search']) || !empty($filters['role']) || !empty($filters['status']))
                    <a href="{{ route('tyro-dashboard.users.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center; padding: 0 12px; flex-shrink: 0;">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Users Table --}}
        <div class="table-responsive overflow-x-auto" style="min-height: 280px;">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>User</th>
                        <th>Designation</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-center w-24">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $listUser)
                        <tr>
                            <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 font-bold text-primary text-xs uppercase dark:bg-primary/20">
                                        {{ strtoupper(substr($listUser->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('tyro-dashboard.users.edit', $listUser->id) }}" class="font-semibold text-dark hover:text-primary dark:text-white-light dark:hover:text-primary transition">
                                            {{ $listUser->name }}
                                        </a>
                                        <div class="text-xs text-white-dark">{{ $listUser->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($listUser->designation)
                                    <span class="badge {{ $listUser->designation === 'senior' ? 'bg-success/10 text-success border border-success/20' : 'bg-secondary/10 text-secondary border border-secondary/20' }} text-[11px] font-semibold px-2 py-0.5 rounded">
                                        {{ ucfirst($listUser->designation) }}
                                    </span>
                                @else
                                    <span class="text-xs text-white-dark">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @forelse($listUser->roles as $role)
                                        <span class="badge bg-primary/10 text-primary border border-primary/20 text-[10px] font-semibold px-2 py-0.5 rounded">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-gray-100 text-gray-500 border border-gray-200 text-[10px] px-2 py-0.5 rounded dark:bg-dark/40 dark:border-dark dark:text-white-dark">
                                            No roles
                                        </span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if(method_exists($listUser, 'isSuspended') && $listUser->isSuspended())
                                    <span class="badge bg-danger/10 text-danger border border-danger/20 font-bold text-[11px] px-2 py-0.5 rounded">
                                        Suspended
                                    </span>
                                @else
                                    <span class="badge bg-success/10 text-success border border-success/20 font-bold text-[11px] px-2 py-0.5 rounded">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="text-xs text-white-dark whitespace-nowrap">
                                {{ $listUser->created_at ? $listUser->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="text-center">
                                {{-- 3-Dot Action Dropdown conforming to AGENTS.md Rule 2 --}}
                                <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                        
                                        {{-- Edit User --}}
                                        <li>
                                            <a href="{{ route('tyro-dashboard.users.edit', $listUser->id) }}" class="flex w-full items-center px-4 py-2 text-left text-primary hover:bg-gray-100 dark:hover:bg-dark/40">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        </li>

                                        {{-- Show Password (Admin / Super Admin Only) --}}
                                        @if(auth()->user()->hasAnyRole(['admin', 'super-admin', 'superadmin']) && !empty($listUser->plain_password))
                                            <li>
                                                <button type="button" 
                                                        class="flex w-full items-center px-4 py-2 text-left text-info hover:bg-gray-100 dark:hover:bg-dark/40"
                                                        @click="toggle(); showPasswordPrompt('{{ addslashes($listUser->name) }}', '{{ addslashes($listUser->plain_password) }}')">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Show Password
                                                </button>
                                            </li>
                                        @endif

                                        {{-- Suspend / Unsuspend --}}
                                        @if(method_exists($listUser, 'isSuspended') && $listUser->isSuspended())
                                            <li>
                                                <form action="{{ route('tyro-dashboard.users.unsuspend', $listUser->id) }}" method="POST" class="m-0"
                                                      onsubmit="return confirm('Are you sure you want to unsuspend {{ addslashes($listUser->name) }}?');">
                                                    @csrf
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-success hover:!bg-success/10 hover:!text-success">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Unsuspend
                                                    </button>
                                                </form>
                                            </li>
                                        @elseif(method_exists($listUser, 'suspend'))
                                            <li>
                                                <button type="button" 
                                                        class="flex w-full items-center px-4 py-2 text-left text-warning hover:bg-gray-100 dark:hover:bg-dark/40"
                                                        @click="toggle(); openSuspendModal({{ $listUser->id }}, '{{ addslashes($listUser->name) }}')">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                    Suspend
                                                </button>
                                            </li>
                                        @endif

                                        {{-- Delete User --}}
                                        @if($listUser->id !== ($user->id ?? auth()->id()))
                                            <li>
                                                <form action="{{ route('tyro-dashboard.users.destroy', $listUser->id) }}" method="POST" class="m-0"
                                                      onsubmit="return confirm('Are you sure you want to delete {{ addslashes($listUser->name) }}? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-danger hover:!bg-danger/10 hover:!text-danger">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-danger" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-white-dark">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="h-10 w-10 text-gray-300 dark:text-dark/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span class="text-sm font-semibold">No users found.</span>
                                    <a href="{{ route('tyro-dashboard.users.create') }}" class="btn btn-primary btn-sm mt-1">Add First User</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="mt-5 border-t border-[#e0e6ed] pt-4 dark:border-[#1b2e4b]">
                {{ $users->links() }}
            </div>
        @endif

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
                            <span>Suspend User Account</span>
                        </h5>
                        <button type="button" @click="suspendModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form :action="suspendFormAction" method="POST">
                        @csrf
                        <div class="p-5 space-y-4">
                            <p class="text-xs text-white-dark leading-relaxed">
                                You are about to suspend <strong class="text-dark dark:text-white font-bold" x-text="suspendTargetName"></strong>. This will revoke all their active sessions and immediately prevent login.
                            </p>
                            <div>
                                <label for="reason" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Reason for suspension <span class="text-white-dark font-normal">(optional)</span>
                                </label>
                                <textarea id="reason" name="reason" rows="3" class="form-textarea text-xs w-full" placeholder="Enter reason for suspension..."></textarea>
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

    {{-- Password Viewer Modal conforming to AGENTS.md Rule 1 --}}
    <template x-teleport="#modal-portal">
        <div x-cloak x-show="pwdModalOpen" class="modal-overlay-full" style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="pwdModalOpen = false"
                     x-show="pwdModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-md rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2 text-primary">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <span>User Password</span>
                        </h5>
                        <button type="button" @click="pwdModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div class="p-5 space-y-4">
                        <p class="text-xs text-white-dark">
                            Plain credentials recorded for <strong class="text-dark dark:text-white" x-text="pwdTargetName"></strong>:
                        </p>
                        <div class="flex items-center justify-between rounded-lg border border-primary/20 bg-primary/5 p-3 text-primary">
                            <code class="font-mono text-sm font-bold tracking-wider" x-text="pwdValue"></code>
                            <button type="button" @click="copyPassword()" class="btn btn-primary btn-xs flex items-center gap-1 font-semibold">
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <button type="button" @click="pwdModalOpen = false" class="btn btn-secondary btn-sm">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('usersPageHandler', () => ({
        suspendModalOpen: false,
        suspendTargetName: '',
        suspendFormAction: '',
        pwdModalOpen: false,
        pwdTargetName: '',
        pwdValue: '',
        copied: false,

        openSuspendModal(userId, userName) {
            this.suspendTargetName = userName;
            this.suspendFormAction = '{{ url(config("tyro-dashboard.route_prefix", "dashboard")) }}/users/' + userId + '/suspend';
            this.suspendModalOpen = true;
        },

        showPasswordPrompt(userName, password) {
            this.pwdTargetName = userName;
            this.pwdValue = password;
            this.copied = false;
            this.pwdModalOpen = true;
        },

        copyPassword() {
            if (navigator.clipboard && this.pwdValue) {
                navigator.clipboard.writeText(this.pwdValue).then(() => {
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2500);
                });
            }
        }
    }));
});
</script>
@endpush
