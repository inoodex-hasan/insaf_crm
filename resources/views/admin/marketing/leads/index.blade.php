@extends('admin.layouts.master')

@section('title', 'Primary Data')

@section('content')
    @php
        $canViewAllLeads = $canViewAllLeads ?? false;
        $collectors = $collectors ?? collect();
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Primary Data</h2>
        @can('*marketing')
            <div class="flex w-full flex-wrap items-center justify-end gap-4 sm:w-auto">
                <a href="{{ route('admin.marketing.leads.create') }}" class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Data
                </a>
            </div>
        @endcan
    </div>

    <div class="panel mt-6">
        <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
            <form action="{{ route('admin.marketing.leads.index') }}" method="GET"
                class="flex w-full flex-1 flex-col gap-5 md:flex-row md:items-center">
                <div class="relative w-full md:w-auto" style="width: 220px;">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Name/Phone..." class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="hover:text-primary absolute inset-y-0 flex items-center ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5"
                                opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <select name="source" class="form-select w-full md:w-auto" style="width: 100px;">
                        <option value="">Sources</option>
                        <option value="Phone" {{ request('source') == 'Phone' ? 'selected' : '' }}>Phone Call</option>
                        <option value="Message" {{ request('source') == 'Message' ? 'selected' : '' }}>WhatsApp/SMS</option>
                        <option value="Messenger" {{ request('source') == 'Messenger' ? 'selected' : '' }}>FB Messenger
                        </option>
                        <option value="Online Chat" {{ request('source') == 'Online Chat' ? 'selected' : '' }}>Website Chat
                        </option>
                        <option value="Walk-in" {{ request('source') == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                    </select>
                    <select name="priority" class="form-select w-full md:w-auto" style="width: 120px;">
                        <option value="">Priority</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    <!-- @if ($canViewAllLeads ?? false)
                        <select name="collected_by" class="form-select w-full min-w-[150px]">
                            <option value="">Collected By</option>
                            @foreach ($collectors ?? [] as $collector)
                                <option value="{{ $collector->id }}"
                                    {{ request('collected_by') == $collector->id ? 'selected' : '' }}>
                                    {{ $collector->name }}</option>
                            @endforeach
                        </select>
                    @endif -->
                    <select name="date_type" class="form-select w-full md:w-auto" style="width: 140px;">
                        <option value="created_at" {{ request('date_type', 'created_at') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                        <option value="next_follow_up_at" {{ request('date_type') == 'next_follow_up_at' ? 'selected' : '' }}>Follow-up Date</option>
                    </select>
                    <input type="date" name="from_date" value="{{ request('from_date', request('created_date', request('created_from', request('follow_up_from')))) }}"
                        class="form-input w-full md:w-auto" style="width: 135px;" title="From Date" />
                    <input type="date" name="to_date" value="{{ request('to_date', request('created_to', request('follow_up_to'))) }}"
                        class="form-input w-full md:w-auto" style="width: 135px;" title="To Date" />
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.marketing.leads.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>

        <div class="datatable">
            <div class="overflow-x-auto">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Phone</th>
                            <th>Priority</th>
                            <th>Contact Source</th>
                            <th>Follow-up Date</th>
                            {{-- <th>Status</th> --}}
                            <th>Created At</th>
                            <!-- <th>Collected By</th> -->
                            @canany(['*marketing', '*consultant'])
                                <th class="text-center">Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $lead->student_name }}</div>
                                    <div class="text-white-dark text-xs">{{ \Illuminate\Support\Str::limit($lead->email, 12) ?? 'No Email' }}</div>
                                </td>
                                <td>
                                    @if ($lead->phone)
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$lead->phone);
                                            if (str_starts_with($cleanPhone, '01') && strlen($cleanPhone) === 11) {
                                                $waNumber = '88' . $cleanPhone;
                                            } elseif (str_starts_with($cleanPhone, '8801') && strlen($cleanPhone) === 13) {
                                                $waNumber = $cleanPhone;
                                            } else {
                                                $waNumber = $cleanPhone;
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 font-medium text-primary hover:text-success hover:underline"
                                            title="Chat on WhatsApp">
                                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            <span>{{ $lead->phone }}</span>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $lead->priority_badge_class }} uppercase text-xs">
                                        {{ $lead->priority ?? 'low' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-outline-primary">{{ $lead->source }}</span>
                                </td>
                                <td>
                                    @php($followUpHistory = collect($lead->follow_up_date_history))
                                    @if ($followUpHistory->isNotEmpty())
                                        @php($currentFollowUpDate = $followUpHistory->last())
                                        <div
                                            class="{{ $currentFollowUpDate->isPast() ? 'text-danger font-bold' : 'font-semibold' }}">
                                            {{ $currentFollowUpDate->format('M d, Y') }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <!-- <td>
                                    <span class="badge @if ($lead->status == 'pending') badge-outline-warning @elseif($lead->status == 'interested') badge-outline-success @elseif($lead->status == 'forwarded') badge-outline-info @else badge-outline-danger @endif capitalize">
                                        {{ $lead->status }}
                                    </span>
                                </td> -->
                                <td>
                                    <span class="badge badge-outline-primary">{{ $lead->created_at->format('M d, Y') }}</span>
                                </td>
                                <!-- <td>
                                    @if ($lead->creator)
                                        <span class="badge badge-outline-primary">{{ $lead->creator->name }}</span>
                                    @else
                                        -
                                    @endif
                                </td> -->
                                @canany(['*marketing', '*consultant'])
                                    <td class="text-center">
                                        <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>
                                            <ul x-cloak x-show="open" x-transition x-transition.duration.300ms class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                                <li>
                                                    <a href="{{ route('admin.marketing.leads.show', $lead->id) }}" @click="toggle">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                        View
                                                    </a>
                                                </li>
                                                @can('*marketing')
                                                    <li>
                                                        <a href="{{ route('admin.marketing.leads.edit', $lead->id) }}" @click="toggle">
                                                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                            </svg>
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.marketing.leads.destroy', $lead->id) }}" method="POST"
                                                            onsubmit="return confirm('Delete this lead?');" class="m-0">
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
                                                @endcan
                                            </ul>
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $leads->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endsection
