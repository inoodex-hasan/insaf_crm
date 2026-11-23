@extends('admin.layouts.master')

@section('title', 'Roles')

@section('content')
<div>

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">Roles</h2>
            <p class="text-xs text-white-dark mt-1">Manage authorization roles, user memberships, and associated privileges.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tyro-dashboard.roles.create') }}" class="btn btn-primary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Add Role</span>
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
                    Total: {{ $roles->total() }} Roles
                </span>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('tyro-dashboard.roles.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                {{-- Search Box --}}
                <div style="position: relative; width: 220px;">
                    <input type="text"
                           name="search"
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Search roles..."
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
                @if(!empty($filters['search']))
                    <a href="{{ route('tyro-dashboard.roles.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center; padding: 0 12px;">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Roles Table --}}
        <div class="table-responsive overflow-x-auto" style="min-height: 280px;">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>Role Name</th>
                        <th>Slug</th>
                        <th>Users</th>
                        <th>Privileges</th>
                        <th>Status</th>
                        <th class="text-center w-24">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-tr from-primary to-indigo-500 text-white shadow-sm">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <a href="{{ route('tyro-dashboard.roles.show', $role->id) }}" class="font-bold text-dark hover:text-primary dark:text-white-light dark:hover:text-primary transition">
                                        {{ $role->name }}
                                    </a>
                                </div>
                            </td>
                            <td>
                                <code class="px-2 py-0.5 rounded text-xs bg-slate-100 font-mono text-primary font-bold dark:bg-dark/60">
                                    {{ $role->slug }}
                                </code>
                            </td>
                            <td>
                                <span class="badge bg-secondary/10 text-secondary border border-secondary/20 text-[11px] font-semibold px-2 py-0.5 rounded">
                                    {{ number_format($role->users_count) }} Users
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info/10 text-info border border-info/20 text-[11px] font-semibold px-2 py-0.5 rounded">
                                    {{ number_format($role->privileges_count) }} Privileges
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('tyro-dashboard.roles.toggle', $role->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" 
                                            class="badge cursor-pointer border transition {{ $role->is_active ? 'bg-success/10 text-success border-success/30 hover:bg-success hover:text-white' : 'bg-danger/10 text-danger border-danger/30 hover:bg-danger hover:text-white' }} font-bold text-[11px] px-2.5 py-1 rounded"
                                            title="Click to toggle status">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
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
                                        
                                        {{-- View Details --}}
                                        <li>
                                            <a href="{{ route('tyro-dashboard.roles.show', $role->id) }}" class="flex w-full items-center px-4 py-2 text-left text-info hover:bg-gray-100 dark:hover:bg-dark/40">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Details
                                            </a>
                                        </li>

                                        {{-- Edit Role --}}
                                        <li>
                                            <a href="{{ route('tyro-dashboard.roles.edit', $role->id) }}" class="flex w-full items-center px-4 py-2 text-left text-primary hover:bg-gray-100 dark:hover:bg-dark/40">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        </li>

                                        {{-- Toggle Status --}}
                                        <li>
                                            <form action="{{ route('tyro-dashboard.roles.toggle', $role->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-warning hover:bg-gray-100 dark:hover:bg-dark/40">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                    {{ $role->is_active ? 'Set Inactive' : 'Set Active' }}
                                                </button>
                                            </form>
                                        </li>

                                        {{-- Delete Role --}}
                                        @if(!in_array($role->slug, $protectedRoles ?? []))
                                            <li>
                                                <form action="{{ route('tyro-dashboard.roles.destroy', $role->id) }}" method="POST" class="m-0"
                                                      onsubmit="return confirm('Are you sure you want to delete the {{ addslashes($role->name) }} role? All assigned users will be unassigned from this role.');">
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
                                        @else
                                            <li>
                                                <span class="flex w-full items-center px-4 py-2 text-left text-gray-400 cursor-not-allowed opacity-60">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    Protected Role
                                                </span>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span class="text-sm font-semibold">No roles found.</span>
                                    <a href="{{ route('tyro-dashboard.roles.create') }}" class="btn btn-primary btn-sm mt-1">Add First Role</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($roles->hasPages())
            <div class="mt-5 border-t border-[#e0e6ed] pt-4 dark:border-[#1b2e4b]">
                {{ $roles->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
