@extends('admin.layouts.master')

@section('title', 'Marketing Campaigns')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Marketing Campaigns</h2>
        @can('*digital_marketing')
            <div class="flex w-full flex-wrap items-center justify-end gap-4 sm:w-auto">
                <a href="{{ route('admin.marketing.campaigns.create') }}" class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    New Campaign
                </a>
            </div>
        @endcan
    </div>

    <div class="panel mt-6">
        <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
            <form action="{{ route('admin.marketing.campaigns.index') }}" method="GET"
                class="flex flex-1 flex-col gap-5 md:flex-row md:items-center w-full">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search campaign name..." class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5"
                                opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    <select name="boosting_status" class="form-select w-auto md:w-auto pr-10">
                        <option value=""> Status</option>
                        <option value="on" {{ request('boosting_status') == 'on' ? 'selected' : '' }}>Boosting ON</option>
                        <option value="off" {{ request('boosting_status') == 'off' ? 'selected' : '' }}>Boosting OFF</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>

        <div class="datatable">
            <div class="overflow-x-auto">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Campaign Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Created By</th>
                            <th>Boosting</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $campaign)
                            <tr>
                                <td>
                                    <div class="font-bold text-lg text-primary">{{ $campaign->name }}</div>
                                    <div class="text-xs text-white-dark">Created: {{ $campaign->created_at->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    <span class="text-sm">{{ $campaign->start_date?->format('M d, Y') ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-sm {{ $campaign->end_date && $campaign->end_date < now() ? 'text-danger' : '' }}">
                                        {{ $campaign->end_date?->format('M d, Y') ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <!-- <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 uppercase">
                                            {{ substr($campaign->creator->name ?? 'U', 0, 1) }}
                                        </div> -->
                                        <span class="text-sm">{{ $campaign->creator->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td x-data="{ 
                                    status: '{{ $campaign->boosting_status }}',
                                    toggle() {
                                        fetch('{{ route('admin.marketing.campaigns.toggle-boosting', $campaign->id) }}', {
                                            method: 'POST',
                                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                        }).then(res => res.json()).then(data => {
                                            this.status = data.boosting_status;
                                        });
                                    }
                                }">
                                    <label class="relative h-6 w-12 items-center cursor-pointer">
                                        <input type="checkbox" class="peer sr-only" :checked="status === 'on'" @change="toggle()" />
                                        <span class="block h-full w-full rounded-full bg-[#ebedf2] before:absolute before:left-1 before:bottom-1 before:h-4 before:w-4 before:rounded-full before:bg-white before:transition-all before:duration-300 peer-checked:bg-primary peer-checked:before:left-7 dark:bg-dark dark:before:bg-white-dark"></span>
                                    </label>
                                    <span class="text-[10px] font-black uppercase mt-1 block" :class="status === 'on' ? 'text-primary' : 'text-slate-400'">
                                        <span x-text="status === 'on' ? 'Boosting ON' : 'OFF'"></span>
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
                                                <a href="{{ route('admin.marketing.campaigns.show', $campaign->id) }}" @click="toggle">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    View
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.marketing.campaigns.edit', $campaign->id) }}" @click="toggle">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.marketing.campaigns.destroy', $campaign->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this campaign and all its assets?');" class="m-0">
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
                                <td colspan="6" class="text-center py-10 text-white-dark">No campaigns found.</td>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
@endsection
