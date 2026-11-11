@extends('admin.layouts.master')

@section('title', 'Countries')

@section('content')

<div x-data="countryModalHandler()">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold uppercase">Countries</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.country-remarks.index') }}" class="btn btn-outline-primary">
                <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Manage Remarks
            </a>
            <button type="button" @click="openCreateModal()" class="btn btn-primary">
                <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Country
            </button>
        </div>
    </div>

    <div class="panel mt-6">
        <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
            <form action="{{ route('admin.countries.index') }}" method="GET"
                class="flex flex-1 flex-col gap-5 md:flex-row md:items-center w-full">
                <div class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search country name or code..." class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $country->name }}</td>
                        <td>
                            <span class="badge {{ $country->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $country->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                                <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                    <li>
                                        <button type="button" 
                                                class="flex w-full items-center px-4 py-2 text-left text-primary hover:bg-gray-100 dark:hover:bg-dark/40"
                                                @click="toggle(); openEditModal({ id: {{ $country->id }}, name: @js($country->name), status: {{ $country->status ? '1' : '0' }} })">
                                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.countries.destroy', $country->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this country?');" class="m-0">
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
                        <td colspan="4" class="text-center py-4">No countries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $countries->links() }}
        </div>
    </div>

    <!-- Modal Teleport Target per Rule 1 -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="modalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="modalOpen = false"
                     x-show="modalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Country' : 'Add New Country'"></h5>
                        <button type="button" @click="modalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Form -->
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <input type="hidden" name="id" :value="form.id">

                        <div class="p-5 space-y-4">
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-dark dark:text-white-light">
                                    Country Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       x-model="form.name" 
                                       class="form-input" 
                                       placeholder="e.g. Canada, Germany, Australia"
                                       required>
                                @error('name')
                                    <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-dark dark:text-white-light">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" x-model="form.status" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('status')
                                    <span class="text-danger text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" x-text="isEdit ? 'Update Country' : 'Save Country'">
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function countryModalHandler() {
        return {
            modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
            isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
            actionUrl: '{{ old('_method') === 'PUT' ? url('/dashboard/countries/' . old('id')) : route('admin.countries.store') }}',
            form: {
                id: '{{ old('id') }}',
                name: @js(old('name', '')),
                status: '{{ old('status', '1') }}'
            },
            openCreateModal() {
                this.isEdit = false;
                this.actionUrl = '{{ route('admin.countries.store') }}';
                this.form = {
                    id: '',
                    name: '',
                    status: '1'
                };
                this.modalOpen = true;
            },
            openEditModal(country) {
                this.isEdit = true;
                this.actionUrl = '{{ url('/dashboard/countries') }}/' + country.id;
                this.form = {
                    id: country.id,
                    name: country.name,
                    status: String(country.status)
                };
                this.modalOpen = true;
            }
        };
    }
</script>

@endsection