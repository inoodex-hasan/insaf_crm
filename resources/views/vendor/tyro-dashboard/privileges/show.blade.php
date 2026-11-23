@extends('admin.layouts.master')

@section('title', $privilege->name . ' - Privilege Details')

@section('content')
<div>

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">{{ $privilege->name }}</h2>
                <code class="px-2 py-0.5 rounded text-xs bg-slate-100 font-mono text-primary font-bold dark:bg-dark/60">{{ $privilege->slug }}</code>
            </div>
            <p class="text-xs text-white-dark mt-1">Review privilege capability and authorization roles assigned.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tyro-dashboard.privileges.edit', $privilege->id) }}" class="btn btn-primary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                <span>Edit Privilege</span>
            </a>
            <a href="{{ route('tyro-dashboard.privileges.index') }}" class="btn btn-secondary gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back to Privileges</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Details Panel --}}
        <div class="panel">
            <h5 class="text-sm font-bold uppercase tracking-wider text-dark dark:text-white-light border-b border-[#e0e6ed] pb-3 mb-4 dark:border-[#1b2e4b]">
                Privilege Specifications
            </h5>

            <div class="space-y-4 text-xs">
                <div>
                    <span class="text-white-dark block mb-1 font-medium">Privilege Name</span>
                    <div class="font-bold text-sm text-dark dark:text-white-light">{{ $privilege->name }}</div>
                </div>

                <div>
                    <span class="text-white-dark block mb-1 font-medium">Slug</span>
                    <code class="px-2 py-1 rounded text-xs bg-slate-100 font-mono text-primary font-bold dark:bg-dark/60">
                        {{ $privilege->slug }}
                    </code>
                </div>

                <div>
                    <span class="text-white-dark block mb-1 font-medium">Description</span>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-dark/40 p-3 rounded-lg border border-slate-100 dark:border-dark">
                        {{ $privilege->description ?: 'No description recorded.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Roles Panel --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-[#e0e6ed] pb-3 mb-4 dark:border-[#1b2e4b]">
                <h5 class="text-sm font-bold uppercase tracking-wider text-dark dark:text-white-light flex items-center gap-2">
                    <span>Roles with this Privilege</span>
                    <span class="badge bg-secondary/10 text-secondary border border-secondary/20 text-xs px-2 py-0.5 rounded">
                        {{ $privilege->roles->count() }}
                    </span>
                </h5>
                <a href="{{ route('tyro-dashboard.privileges.edit', $privilege->id) }}" class="text-xs font-semibold text-primary hover:underline">
                    Manage
                </a>
            </div>

            @if($privilege->roles->count())
                <div class="table-responsive overflow-x-auto">
                    <table class="table-auto w-full text-xs">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Slug</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($privilege->roles as $role)
                                <tr>
                                    <td>
                                        <a href="{{ route('tyro-dashboard.roles.show', $role->id) }}" class="font-bold text-dark hover:text-primary dark:text-white-light dark:hover:text-primary transition">
                                            {{ $role->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <code class="px-2 py-0.5 rounded text-[11px] bg-slate-100 font-mono text-primary font-bold dark:bg-dark/60">
                                            {{ $role->slug }}
                                        </code>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-white-dark text-xs">
                    No roles are currently assigned this privilege.
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
