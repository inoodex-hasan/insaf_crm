@extends('admin.layouts.master')

@section('title', 'Country Remarks')

@section('content')
<div x-data="countryRemarksManager()">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold uppercase">Country Remarks</h2>
            <p class="text-xs text-white-dark mt-1">
                Configure destination-specific pipeline remarks & milestone badges for Malta, Russia, and Malaysia.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-primary gap-1.5">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Countries
            </a>
            <button type="button" @click="openCreateModal()" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New Remark
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-5 flex items-center rounded-lg bg-success/10 border border-success/20 p-3.5 text-success">
            <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 flex items-center rounded-lg bg-danger/10 border border-danger/20 p-3.5 text-danger">
            <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Panel with Integrated Filter Tabs and Search Bar -->
    <div class="panel">
        
        <!-- Toolbar: Country Filter Tabs & Keyword Search -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            
            <!-- Country Tabs -->
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route('admin.country-remarks.index', array_merge(request()->except('country_id'), ['country_id' => 'all'])) }}"
                   class="btn btn-sm {{ ($selectedCountryId === 'all' || empty($selectedCountryId)) ? 'btn-primary' : 'btn-outline-primary' }}"
                   style="border-radius: 8px; font-size: 12px; font-weight: 600; padding: 6px 14px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                    <span>All Countries</span>
                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; border-radius: 9999px; font-size: 11px; font-weight: 700; padding: 0 6px; {{ ($selectedCountryId === 'all' || empty($selectedCountryId)) ? 'background: rgba(255, 255, 255, 0.28); color: #ffffff;' : 'background: rgba(67, 97, 238, 0.12); color: #4361ee;' }}">
                        {{ $counts['total'] ?? 0 }}
                    </span>
                </a>

                @foreach($countries as $country)
                    @php
                        $isActive = (string)$selectedCountryId === (string)$country->id;
                    @endphp
                    <a href="{{ route('admin.country-remarks.index', array_merge(request()->except('country_id'), ['country_id' => $country->id])) }}"
                       class="btn btn-sm {{ $isActive ? 'btn-primary' : 'btn-outline-primary' }}"
                       style="border-radius: 8px; font-size: 12px; font-weight: 600; padding: 6px 14px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                        <span>{{ $country->name }}</span>
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; border-radius: 9999px; font-size: 11px; font-weight: 700; padding: 0 6px; {{ $isActive ? 'background: rgba(255, 255, 255, 0.28); color: #ffffff;' : 'background: rgba(67, 97, 238, 0.12); color: #4361ee;' }}">
                            {{ $counts[$country->id] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>

            <!-- Search Filter (Pushed cleanly to the right) -->
            <form action="{{ route('admin.country-remarks.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto;">
                @if(!empty($selectedCountryId) && $selectedCountryId !== 'all')
                    <input type="hidden" name="country_id" value="{{ $selectedCountryId }}">
                @endif
                <div style="position: relative; width: 220px;">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search remark by name..."
                           class="form-input text-xs w-full"
                           style="height: 36px; padding-right: 36px; border-radius: 6px;" />
                    <button type="submit"
                            style="position: absolute; right: 8px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; background: none; border: none; padding: 0; cursor: pointer;"
                            class="text-gray-400 hover:text-primary"
                            title="Search">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.country-remarks.index', ['country_id' => $selectedCountryId ?? 'all']) }}"
                       class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center; border-radius: 6px;">
                        Clear
                    </a>
                @endif
            </form>

        </div>

        <!-- Remarks Table -->
        <div class="datatable">
            <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
                <table class="table-hover w-full table-auto text-left text-sm">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="min-width: 120px;">Country</th>
                            <th style="min-width: 220px;">Remark / Milestone</th>
                            <th style="min-width: 180px;">Badge Preview</th>
                            <th class="text-center" style="width: 90px;">Order</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            <th class="text-center" style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remarks as $remark)
                            <tr>
                                <td class="font-semibold text-gray-500 text-xs">{{ $loop->iteration }}</td>
                                
                                <!-- Country Badge -->
                                <td>
                                    @php
                                        $cName = strtolower(trim($remark->country?->name ?? ''));
                                        $countryBadge = match($cName) {
                                            'malta' => 'badge-outline-primary',
                                            'russia' => 'badge-outline-secondary',
                                            'malaysia' => 'badge-outline-info',
                                            default => 'badge-outline-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $countryBadge }} text-xs font-semibold py-0.5 px-2.5">
                                        {{ $remark->country->name ?? 'All Countries' }}
                                    </span>
                                </td>

                                <!-- Remark Name -->
                                <td>
                                    <div class="font-semibold text-gray-800 dark:text-white-light text-sm">
                                        {{ $remark->name }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        Color Style: <span class="capitalize text-primary font-medium">{{ $remark->color }}</span>
                                    </div>
                                </td>

                                <!-- Badge Preview -->
                                <td>
                                    <span class="badge {{ $remark->badge_class }} font-semibold text-xs py-1 px-3 inline-block">
                                        {{ $remark->name }}
                                    </span>
                                </td>

                                <!-- Sort Order -->
                                <td class="text-center">
                                    <span class="font-mono text-xs px-2.5 py-1 rounded bg-gray-100 dark:bg-[#121e32] text-gray-700 dark:text-gray-300 font-semibold border border-gray-200 dark:border-gray-700">
                                        {{ $remark->sort_order }}
                                    </span>
                                </td>

                                <!-- Active Status Toggle -->
                                <td class="text-center">
                                    <form action="{{ route('admin.country-remarks.toggle-status', $remark) }}" method="POST" class="inline-block m-0">
                                        @csrf
                                        <button type="submit"
                                                title="Click to toggle status"
                                                class="badge cursor-pointer transition {{ $remark->is_active ? 'badge-outline-success hover:bg-success hover:text-white' : 'badge-outline-danger hover:bg-danger hover:text-white' }}"
                                                style="padding: 4px 10px; font-weight: 600;">
                                            {{ $remark->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>

                                <!-- 3-Dot Action Dropdown -->
                                <td class="text-center">
                                    <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'"
                                            class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-md py-1.5"
                                            style="min-width: 140px;">
                                            <li>
                                                <button type="button"
                                                        @click="openEditModal({{ json_encode($remark) }}); toggle();"
                                                        class="flex w-full items-center px-4 py-2 text-left text-xs font-medium text-primary hover:bg-gray-100 dark:hover:bg-dark/40">
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
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-xs font-medium text-danger hover:bg-gray-100 dark:hover:bg-dark/40">
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
                                <td colspan="7" class="py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="h-10 w-10 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">No remarks found</div>
                                        <p class="text-xs text-gray-400">There are no remarks configured for this filter yet.</p>
                                        <button type="button" @click="openCreateModal()" class="btn btn-primary btn-sm mt-2 text-xs">
                                            + Add Remark
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Create / Edit Remark Modal (Protocol per AGENTS.md) -->
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

                    <!-- Form Body -->
                    <form :action="formAction" method="POST" class="p-5 space-y-4">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Country Destination -->
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">
                                Destination Country <span class="text-danger">*</span>
                            </label>
                            <select name="country_id" x-model="formData.country_id" class="form-select w-full text-sm" required>
                                <option value="">-- Select Country --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Remark / Milestone Name -->
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">
                                Remark / Milestone Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   x-model="formData.name"
                                   placeholder="e.g. Offer Letter Pending, EMGS - 35%, Flight Done..."
                                   class="form-input w-full text-sm"
                                   required>
                        </div>

                        <!-- Badge Color & Sort Order (Explicit Flex Row) -->
                        <div style="display: flex; gap: 14px; width: 100%;">
                            <div style="flex: 1; min-width: 140px;">
                                <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">
                                    Badge Color Style
                                </label>
                                <select name="color" x-model="formData.color" class="form-select w-full text-sm">
                                    @foreach($colorOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="flex: 1; min-width: 120px;">
                                <label class="block text-xs font-semibold mb-1.5 text-dark dark:text-white-light">
                                    Sort Order
                                </label>
                                <input type="number"
                                       name="sort_order"
                                       x-model="formData.sort_order"
                                       min="0"
                                       step="5"
                                       placeholder="10"
                                       class="form-input w-full text-sm">
                            </div>
                        </div>

                        <!-- Live Interactive Badge Preview -->
                        <div class="rounded-lg border border-[#e0e6ed] p-3 dark:border-[#1b2e4b] bg-[#fbfbfb] dark:bg-[#121e32]">
                            <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Live Badge Preview
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge font-semibold text-xs py-1 px-3"
                                      :class="previewBadgeClass"
                                      x-text="formData.name ? formData.name : 'Sample Milestone Preview'">
                                </span>
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
                                <span class="text-xs font-semibold text-dark dark:text-white-light ltr:ml-2 rtl:mr-2">
                                    Active (Visible in student remarks dropdown)
                                </span>
                            </label>
                        </div>

                        <!-- Modal Actions Footer -->
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
@endsection

@push('scripts')
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

        get previewBadgeClass() {
            const color = this.formData.color || 'primary';
            switch (color) {
                case 'success':
                    return 'bg-success/10 text-success border border-success/30';
                case 'danger':
                    return 'bg-danger/10 text-danger border border-danger/30';
                case 'warning':
                    return 'bg-warning/10 text-warning border border-warning/30';
                case 'info':
                    return 'bg-info/10 text-info border border-info/30';
                case 'secondary':
                    return 'bg-secondary/10 text-secondary border border-secondary/30';
                case 'dark':
                    return 'bg-dark/10 text-dark border border-dark/30';
                default:
                    return 'bg-primary/10 text-primary border border-primary/30';
            }
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
@endpush
