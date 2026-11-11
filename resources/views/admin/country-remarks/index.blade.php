@extends('admin.layouts.master')

@section('title', 'Country Remarks')

@section('content')
<div x-data="countryRemarksManager()" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold uppercase">Country Remarks</h2>
            <p class="text-xs text-white-dark mt-1">
                Dynamic milestone stages configured for Malta, Russia, and Malaysia.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="openCreateModal()" class="btn btn-primary">
                <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Add New Remark
            </button>
        </div>
    </div>

    <!-- Country Filter Tabs -->
    <div class="panel p-3">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-sm font-medium">
            <a href="{{ route('admin.country-remarks.index', ['country_id' => 'all']) }}"
               class="px-4 py-2 rounded-md whitespace-nowrap transition-colors {{ ($selectedCountryId === 'all' || empty($selectedCountryId)) ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                All Countries
                <span class="ltr:ml-1 rtl:mr-1 text-xs opacity-80">({{ $counts['total'] ?? 0 }})</span>
            </a>

            @foreach($countries as $country)
                <a href="{{ route('admin.country-remarks.index', ['country_id' => $country->id]) }}"
                   class="px-4 py-2 rounded-md whitespace-nowrap transition-colors {{ (string)$selectedCountryId === (string)$country->id ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    {{ $country->name }}
                    <span class="ltr:ml-1 rtl:mr-1 text-xs opacity-80">({{ $counts[$country->id] ?? 0 }})</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Remarks Table in Standard CRM Design -->
    <div class="panel">
        <div class="table-responsive">
            <table class="table-hover table-striped w-full table-auto">
                <thead>
                    <tr>
                        <th class="w-16">#</th>
                        <th>Country</th>
                        <th>Remark / Milestone</th>
                        <th>Badge Style</th>
                        <th class="text-center w-24">Order</th>
                        <th class="text-center w-28">Status</th>
                        <th class="text-center w-24">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($remarks as $remark)
                        <tr>
                            <td class="font-semibold text-white-dark">{{ $loop->iteration }}</td>
                            <td>
                                @php
                                    $cName = strtolower(trim($remark->country?->name ?? ''));
                                    $countryBadge = match($cName) {
                                        'malta' => 'bg-primary',
                                        'russia' => 'bg-secondary',
                                        'malaysia' => 'bg-info',
                                        default => 'bg-dark',
                                    };
                                @endphp
                                <span class="badge {{ $countryBadge }} text-xs font-semibold">
                                    {{ $remark->country->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="font-semibold text-gray-800 dark:text-white-light text-sm">
                                    {{ $remark->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $remark->badge_class }} font-semibold text-xs py-1 px-2.5">
                                    {{ $remark->name }}
                                </span>
                            </td>
                            <td class="text-center font-mono text-xs text-white-dark">
                                {{ $remark->sort_order }}
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.country-remarks.toggle-status', $remark) }}" method="POST" class="inline-block m-0">
                                    @csrf
                                    <button type="submit"
                                            title="Click to toggle status"
                                            class="badge cursor-pointer transition hover:opacity-80 {{ $remark->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $remark->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
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
                                                    @click="openEditModal({{ json_encode($remark) }}); toggle();"
                                                    class="flex w-full items-center px-4 py-2 text-left text-primary hover:!bg-primary/10 hover:!text-primary">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.country-remarks.destroy', $remark->id) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this remark?');" class="m-0">
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
                            <td colspan="7" class="py-8 text-center text-white-dark">
                                No remarks found for the selected country.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create / Edit Remark Modal -->
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
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEditing ? 'Edit Country Remark' : 'Add New Country Remark'"></h5>
                        <button type="button" @click="modalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form :action="formAction" method="POST" class="p-5 space-y-4">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Country Destination (Strictly Malta, Russia, Malaysia) -->
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">
                                Country Destination <span class="text-danger">*</span>
                            </label>
                            <select name="country_id" x-model="formData.country_id" class="form-select w-full" required>
                                <option value="">-- Select Country --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Remark Name -->
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">
                                Remark / Milestone Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   x-model="formData.name"
                                   placeholder="e.g. Offer Letter Pending, EMGS - 35%, Flight Done..."
                                   class="form-input w-full"
                                   required>
                        </div>

                        <!-- Color and Order Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">
                                    Badge Color Style
                                </label>
                                <select name="color" x-model="formData.color" class="form-select w-full">
                                    @foreach($colorOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">
                                    Sort Order (Sequence)
                                </label>
                                <input type="number"
                                       name="sort_order"
                                       x-model="formData.sort_order"
                                       min="0"
                                       step="5"
                                       class="form-input w-full">
                            </div>
                        </div>

                        <!-- Active Toggle -->
                        <div class="pt-1">
                            <label class="inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       x-model="formData.is_active"
                                       class="form-checkbox text-primary rounded" />
                                <span class="text-xs font-semibold text-dark dark:text-white-light">
                                    Active (Visible in student remarks dropdown)
                                </span>
                            </label>
                        </div>

                        <!-- Modal Actions -->
                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-[#e0e6ed] pt-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-8" x-text="isEditing ? 'Save Changes' : 'Create Remark'">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
function countryRemarksManager() {
    return {
        modalOpen: false,
        isEditing: false,
        formAction: '{{ route('admin.country-remarks.store') }}',
        formData: {
            country_id: '{{ $selectedCountryId && $selectedCountryId !== "all" ? $selectedCountryId : ($countries->first()->id ?? "") }}',
            name: '',
            color: 'primary',
            sort_order: 10,
            is_active: true
        },

        openCreateModal() {
            this.isEditing = false;
            this.formAction = '{{ route('admin.country-remarks.store') }}';
            this.formData = {
                country_id: '{{ $selectedCountryId && $selectedCountryId !== "all" ? $selectedCountryId : ($countries->first()->id ?? "") }}',
                name: '',
                color: 'primary',
                sort_order: 10,
                is_active: true
            };
            this.modalOpen = true;
        },

        openEditModal(remark) {
            this.isEditing = true;
            this.formAction = '{{ url('dashboard/country-remarks') }}/' + remark.id;
            this.formData = {
                country_id: String(remark.country_id),
                name: remark.name,
                color: remark.color || 'primary',
                sort_order: remark.sort_order || 0,
                is_active: Boolean(remark.is_active)
            };
            this.modalOpen = true;
        }
    };
}
</script>
@endsection
