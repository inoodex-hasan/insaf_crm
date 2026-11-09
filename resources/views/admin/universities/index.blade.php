@extends('admin.layouts.master')

@section('title', 'Universities')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold uppercase">Universities</h2>
        @can('create-university')
            <a href="{{ route('admin.universities.create') }}" class="btn btn-primary">Add University</a>
        @endcan
    </div>

    <div class="panel mt-6">
        <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
            <form action="{{ route('admin.universities.index') }}" method="GET"
                class="flex flex-1 flex-col gap-5 md:flex-row md:items-center w-full">
                <div class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search university name..."
                        class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5"
                                opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    <select name="country_id" class="form-select w-full md:w-80 pr-10">
                        <option value="">All Countries</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.universities.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Country</th>
                    <th>Name</th>
                    <th>Short Name</th>
                    <th>Email</th>
                    <th>Website</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($universities as $university)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $university->country->name ?? 'N/A' }}</td>
                        <td>{{ $university->name ?? 'N/A' }}</td>
                        <td>{{ $university->short_name ?? 'N/A' }}</td>
                        <td>{{ $university->email ?? 'N/A' }}</td>
                        <td>{{ $university->website ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $university->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $university->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                                <ul x-cloak x-show="open" x-transition x-transition.duration.300ms class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                    <li>
                                        <a href="{{ route('admin.universities.edit', $university->id) }}" @click="toggle">
                                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.universities.destroy', $university->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this university?');" class="m-0">
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
                        <td colspan="8" class="text-center py-4">No universities found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $universities->links() }}
        </div>
    </div>

@endsection