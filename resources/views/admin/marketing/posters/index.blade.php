@extends('admin.layouts.master')

@section('title', 'Marketing Posters')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Marketing Posters</h2>
    </div>

    <!-- Add New Poster -->
    <div class="panel mt-6">
        <h3 class="text-lg font-bold mb-4">Add New Poster</h3>
        <form action="{{ route('admin.marketing.posters.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <input type="text" name="poster_name" placeholder="Poster name" class="form-input" required />
            </div>
            <div>
                <select name="status" class="form-select">
                    <option value="pending">Pending</option>
                    <option value="not_ready">Not Ready</option>
                    <option value="designing">Designing</option>
                    <option value="ready">Ready</option>
                    <option value="uploaded">Uploaded</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Add Poster</button>
            </div>
        </form>
    </div>

    <!-- Filter -->
    <div class="panel mt-6">
        <form action="{{ route('admin.marketing.posters.index') }}" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search posters..." class="form-input w-full md:w-auto" />
            <select name="status" class="form-select w-full md:w-auto">
                <option value="">Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="not_ready" {{ request('status') == 'not_ready' ? 'selected' : '' }}>Not Ready</option>
                <option value="designing" {{ request('status') == 'designing' ? 'selected' : '' }}>Designing</option>
                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                <option value="uploaded" {{ request('status') == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.marketing.posters.index') }}" class="btn btn-outline-primary">Reset</a>
        </form>
    </div>

    <!-- Posters Table -->
    <div class="panel mt-6">
        <div class="datatable">
            <div class="overflow-x-auto">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Poster Name</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posters as $poster)
                            <tr>
                                <td>{{ $poster->poster_name }}</td>
                                <td>
                                    @php
                                        $pClass = [
                                            'pending' => 'badge-outline-dark',
                                            'not_ready' => 'badge-outline-danger',
                                            'designing' => 'badge-outline-warning',
                                            'ready' => 'badge-outline-success',
                                            'uploaded' => 'badge-outline-primary',
                                        ][$poster->status] ?? 'badge-outline-dark';
                                    @endphp
                                    <span class="badge {{ $pClass }} capitalize text-[10px]">{{ str_replace('_', ' ', $poster->status) }}</span>
                                </td>
                                <td>{{ $poster->created_at->format('M d, Y') }}</td>
                                <td class="text-center">
                                    <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                            <li>
                                                <a href="{{ route('admin.marketing.posters.edit', $poster) }}" @click="toggle">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.marketing.posters.destroy', $poster) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this poster?');" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-danger hover:!bg-danger/10 hover:!text-danger">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-danger" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-10 text-white-dark">No posters found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $posters->links() }}
            </div>
        </div>
    </div>
@endsection
