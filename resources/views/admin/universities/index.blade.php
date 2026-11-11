@extends('admin.layouts.master')

@section('title', 'Universities')

@section('content')

<div x-data="universityModalHandler()">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold uppercase">Universities</h2>
        @can('create-university')
            <button type="button" @click="openCreateModal()" class="btn btn-primary">Add University</button>
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
                        <td>{{ $university->email ? \Illuminate\Support\Str::limit($university->email, 20) : 'N/A' }}</td>
                        <td>{{ $university->website ? \Illuminate\Support\Str::limit($university->website, 20) : 'N/A' }}</td>
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
                                <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                    <li>
                                        <button type="button" @click="toggle(); openEditModal({
                                            id: {{ $university->id }},
                                            country_id: '{{ $university->country_id }}',
                                            state_province: @js($university->state_province ?? ''),
                                            name: @js($university->name),
                                            short_name: @js($university->short_name ?? ''),
                                            email: @js($university->email ?? ''),
                                            website: @js($university->website ?? ''),
                                            phone: @js($university->phone ?? ''),
                                            address: @js($university->address ?? ''),
                                            status: {{ $university->status ? 1 : 0 }}
                                        })" class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            Edit
                                        </button>
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

    {{-- Wider Modal for University (max-w-2xl) --}}
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
                     class="w-full max-w-2xl rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit University' : 'Create University'"></h5>
                        <button type="button" @click="modalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <input type="hidden" name="_method" x-show="isEdit" value="PUT">

                        <div class="p-6 space-y-4">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label>Country <span class="text-danger">*</span></label>
                                    <select name="country_id" class="form-select" x-model="form.country_id" required>
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Province / State (Optional)</label>
                                    <input type="text" name="state_province" class="form-input" placeholder="e.g. Selangor, Moscow Oblast" x-model="form.state_province">
                                </div>

                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-input" x-model="form.name" required placeholder="University Name">
                                </div>

                                <div class="form-group">
                                    <label>Short Name (Optional)</label>
                                    <input type="text" name="short_name" class="form-input" x-model="form.short_name" placeholder="e.g. UTM, MSU">
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-input" x-model="form.email" placeholder="contact@university.edu">
                                </div>

                                <div class="form-group">
                                    <label>Website</label>
                                    <input type="url" name="website" class="form-input" x-model="form.website" placeholder="https://university.edu">
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-input" x-model="form.phone" placeholder="+123456789">
                                </div>

                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" x-model="form.status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label>Address</label>
                                    <textarea name="address" rows="3" class="form-textarea" x-model="form.address" placeholder="Campus address..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] px-6 py-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-primary px-8" x-text="isEdit ? 'Update University' : 'Save University'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function universityModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
        actionUrl: '{{ route('admin.universities.store') }}',
        form: {
            country_id: @js(old('country_id', '')),
            state_province: @js(old('state_province', '')),
            name: @js(old('name', '')),
            short_name: @js(old('short_name', '')),
            email: @js(old('email', '')),
            website: @js(old('website', '')),
            phone: @js(old('phone', '')),
            address: @js(old('address', '')),
            status: @js(old('status', 1)),
        },
        openCreateModal() {
            this.isEdit = false;
            this.actionUrl = '{{ route('admin.universities.store') }}';
            this.form = {
                country_id: '',
                state_province: '',
                name: '',
                short_name: '',
                email: '',
                website: '',
                phone: '',
                address: '',
                status: 1
            };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.actionUrl = '{{ url('dashboard/universities') }}/' + item.id;
            this.form = {
                country_id: item.country_id,
                state_province: item.state_province,
                name: item.name,
                short_name: item.short_name,
                email: item.email,
                website: item.website,
                phone: item.phone,
                address: item.address,
                status: item.status
            };
            this.modalOpen = true;
        }
    };
}
</script>

@endsection
