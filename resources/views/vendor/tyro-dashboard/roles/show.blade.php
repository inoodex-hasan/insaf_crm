@extends('admin.layouts.master')

@section('title', $role->name . ' - Role Details')

@section('content')
<div>

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">{{ $role->name }}</h2>
                <code class="px-2 py-0.5 rounded text-xs bg-slate-100 font-mono text-primary font-bold dark:bg-dark/60">{{ $role->slug }}</code>
                <span class="badge {{ $role->is_active ? 'bg-success/10 text-success border border-success/20' : 'bg-danger/10 text-danger border border-danger/20' }} font-bold text-xs px-2.5 py-0.5 rounded">
                    {{ $role->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="text-xs text-white-dark mt-1">Review permissions and member staff assigned to this role.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tyro-dashboard.roles.edit', $role->id) }}" class="btn btn-primary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                <span>Edit Role</span>
            </a>
            <a href="{{ route('tyro-dashboard.roles.index') }}" class="btn btn-secondary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back to Roles</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Privileges Section --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-[#e0e6ed] pb-4 mb-4 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-dark dark:text-white-light flex items-center gap-2">
                    <span>Assigned Privileges</span>
                    <span class="badge bg-primary/10 text-primary border border-primary/20 text-xs px-2 py-0.5 rounded">
                        {{ $role->privileges->count() }}
                    </span>
                </h5>
                <a href="{{ route('tyro-dashboard.roles.edit', $role->id) }}" class="text-xs font-semibold text-primary hover:underline">
                    Manage
                </a>
            </div>

            @if($role->privileges->count())
                <div class="table-responsive overflow-x-auto">
                    <table class="table-auto w-full text-xs">
                        <thead>
                            <tr>
                                <th>Privilege</th>
                                <th>Slug</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->privileges as $privilege)
                                <tr>
                                    <td>
                                        <a href="{{ route('tyro-dashboard.privileges.show', $privilege->id) }}" class="font-bold text-dark hover:text-primary dark:text-white-light dark:hover:text-primary transition">
                                            {{ $privilege->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <code class="px-2 py-0.5 rounded text-[11px] bg-slate-100 font-mono text-primary font-bold dark:bg-dark/60">
                                            {{ $privilege->slug }}
                                        </code>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-white-dark text-xs">
                    No privileges assigned to this role yet.
                </div>
            @endif
        </div>

        {{-- Users Section --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-[#e0e6ed] pb-4 mb-4 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-dark dark:text-white-light flex items-center gap-2">
                    <span>Users with this Role</span>
                    <span class="badge bg-secondary/10 text-secondary border border-secondary/20 text-xs px-2 py-0.5 rounded">
                        {{ $role->users->count() }}
                    </span>
                </h5>
                @if($role->users->count() > 10)
                    <a href="{{ route('tyro-dashboard.users.index', ['role' => $role->slug]) }}" class="text-xs font-semibold text-primary hover:underline">
                        View All
                    </a>
                @endif
            </div>

            @if($role->users->count())
                <div class="table-responsive overflow-x-auto">
                    <table class="table-auto w-full text-xs">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th class="text-center w-20">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users->take(10) as $roleUser)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-bold text-[10px] uppercase dark:bg-primary/20">
                                                {{ strtoupper(substr($roleUser->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('tyro-dashboard.users.edit', $roleUser->id) }}" class="font-bold text-dark hover:text-primary dark:text-white-light dark:hover:text-primary transition">
                                                    {{ $roleUser->name }}
                                                </a>
                                                <div class="text-[11px] text-white-dark">{{ $roleUser->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('tyro-dashboard.roles.remove-user', ['id' => $role->id, 'userId' => $roleUser->id]) }}" method="POST"
                                              onsubmit="return confirm('Remove {{ addslashes($roleUser->name) }} from the {{ addslashes($role->name) }} role?');" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-danger hover:underline text-xs font-semibold" title="Remove user from role">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-white-dark text-xs">
                    No users currently have this role.
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
