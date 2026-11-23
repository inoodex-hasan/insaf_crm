@extends('admin.layouts.master')

@section('title', 'Primary Data')

@section('content')
<div x-data="leadsIndexHub()">
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
        <div class="mb-5">
            <form action="{{ route('admin.marketing.leads.index') }}" method="GET" style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                
                <!-- Toolbar: KPI summary & Quick Search / Primary Filters -->
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 12px;" class="dark:border-[#1b2e4b]">
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                            Total: {{ $totalCount ?? $leads->total() }} Leads
                        </span>
                        <span class="badge bg-danger/10 text-danger border border-danger/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                            High Priority: {{ $highPriorityCount ?? 0 }}
                        </span>
                        <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                            Today: {{ $todayCount ?? 0 }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                        <select name="source" class="form-select text-xs" style="height: 36px; width: 130px; border-radius: 6px;" onchange="this.form.submit()">
                            <option value="">All Sources</option>
                            <option value="Phone" {{ request('source') == 'Phone' ? 'selected' : '' }}>Phone Call</option>
                            <option value="Message" {{ request('source') == 'Message' ? 'selected' : '' }}>WhatsApp/SMS</option>
                            <option value="Messenger" {{ request('source') == 'Messenger' ? 'selected' : '' }}>FB Messenger</option>
                            <option value="Online Chat" {{ request('source') == 'Online Chat' ? 'selected' : '' }}>Website Chat</option>
                            <option value="Walk-in" {{ request('source') == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                        </select>

                        <select name="priority" class="form-select text-xs" style="height: 36px; width: 120px; border-radius: 6px;" onchange="this.form.submit()">
                            <option value="">All Priorities</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>

                        @if($canViewAllLeads && $collectors->isNotEmpty())
                            <select name="collected_by" class="form-select text-xs" style="height: 36px; width: 140px; border-radius: 6px;" onchange="this.form.submit()">
                                <option value="">All Collectors</option>
                                @foreach($collectors as $collector)
                                    <option value="{{ $collector->id }}" {{ request('collected_by') == $collector->id ? 'selected' : '' }}>
                                        {{ $collector->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        <div style="position: relative; width: 220px;">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search name, phone..."
                                   class="form-input text-xs w-full"
                                   style="height: 36px; padding-right: 36px; border-radius: 6px;" />
                            <button type="submit"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; cursor: pointer;"
                                    class="text-gray-400 hover:text-primary">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>
                        </div>

                        @if(request('search') || request('source') || request('priority') || request('collected_by') || request('from_date') || request('to_date'))
                            <a href="{{ route('admin.marketing.leads.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                        @endif
                    </div>
                </div>

                <!-- Secondary Row: Date Range Filter -->
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap;">
                    <span class="text-xs text-gray-500 font-medium">Date Filter:</span>
                    <select name="date_type" class="form-select text-xs" style="height: 34px; width: 130px; border-radius: 6px;">
                        <option value="created_at" {{ request('date_type', 'created_at') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                        <option value="next_follow_up_at" {{ request('date_type') == 'next_follow_up_at' ? 'selected' : '' }}>Follow-up Date</option>
                    </select>
                    <input type="date" name="from_date" value="{{ request('from_date', request('created_from', request('follow_up_from'))) }}" class="form-input text-xs" style="height: 34px; width: 140px; border-radius: 6px;" title="From Date">
                    <span class="text-xs text-gray-400">to</span>
                    <input type="date" name="to_date" value="{{ request('to_date', request('created_to', request('follow_up_to'))) }}" class="form-input text-xs" style="height: 34px; width: 140px; border-radius: 6px;" title="To Date">
                    <button type="submit" class="btn btn-primary btn-sm text-xs" style="height: 34px;">Apply</button>
                </div>
            </form>
        </div>

        <div class="datatable">
            <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Phone</th>
                            <th>Priority</th>
                            <th>Contact Source</th>
                            <th>Follow-up Date</th>
                            <th>Created At</th>
                            @canany(['*marketing', '*consultant'])
                                <th class="text-center">Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td>
                                    <div>
                                        <button type="button" 
                                                @click="openLeadModal({{ json_encode($lead) }}, {{ json_encode($lead->country) }}, {{ json_encode($lead->course) }}, {{ json_encode($lead->creator) }})"
                                                class="font-semibold text-primary hover:underline text-left cursor-pointer"
                                                title="Click to view details in modal">
                                            {{ $lead->student_name }}
                                        </button>
                                    </div>
                                    <div class="text-white-dark text-xs">{{ \Illuminate\Support\Str::limit($lead->email, 18) ?? 'No Email' }}</div>
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
                                <td>
                                    <span class="badge badge-outline-primary">{{ $lead->created_at->format('M d, Y') }}</span>
                                </td>
                                @canany(['*marketing', '*consultant'])
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
                                                            @click="openLeadModal({{ json_encode($lead) }}, {{ json_encode($lead->country) }}, {{ json_encode($lead->course) }}, {{ json_encode($lead->creator) }}); toggle();" 
                                                            class="flex w-full items-center px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-dark/40 cursor-pointer">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                        View
                                                    </button>
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

    <!-- VIEW LEAD MODAL - STRICT PROTOCOL (AGENTS.md RULE 1) -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="leadModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="leadModalOpen = false"
                     x-show="leadModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-3xl rounded-xl border border-[#e0e6ed] bg-white shadow-2xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden"
                     style="max-height: 90vh; display: flex; flex-direction: column;">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge uppercase text-[10px] font-bold px-2 py-0.5 rounded"
                                      :class="priorityBadgeClass"
                                      x-text="(selectedLead?.priority || 'low') + ' Priority'"></span>
                                <span class="badge uppercase text-[10px] font-bold px-2 py-0.5 rounded capitalize"
                                      :class="statusBadgeClass"
                                      x-text="selectedLead?.status || 'Pending'"></span>
                            </div>
                            <h5 class="text-base font-bold text-dark dark:text-white-light mt-1" x-text="selectedLead?.student_name || 'Lead Details'"></h5>
                        </div>
                        <button type="button" 
                                @click="leadModalOpen = false" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-dark hover:bg-slate-100 dark:hover:text-white dark:hover:bg-[#1b2e4b] transition">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable) -->
                    <div class="p-6 space-y-5 overflow-y-auto" style="flex: 1 1 auto; max-height: calc(90vh - 140px);">
                        
                        <!-- Contact & Personal Info -->
                        <div class="bg-slate-50 dark:bg-[#121e32] p-4 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] text-xs space-y-3">
                            <span class="text-white-dark block text-[10px] uppercase font-bold tracking-wider">Contact & Ingestion Profile</span>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-white-dark block text-[11px] mb-0.5">Phone Number:</span>
                                    <div class="flex items-center gap-2">
                                        <template x-if="selectedLead?.phone">
                                            <div class="flex items-center gap-2">
                                                <a :href="'tel:' + cleanPhone" class="font-bold text-dark dark:text-white-light hover:underline text-sm" x-text="selectedLead.phone"></a>
                                                <a :href="whatsAppUrl" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-success hover:underline bg-success/10 px-2 py-0.5 rounded border border-success/20">
                                                    <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                                    </svg>
                                                    <span>WhatsApp</span>
                                                </a>
                                            </div>
                                        </template>
                                        <template x-if="!selectedLead?.phone">
                                            <span class="text-slate-400">N/A</span>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-white-dark block text-[11px] mb-0.5">Email Address:</span>
                                    <span class="font-bold text-dark dark:text-white-light" x-text="selectedLead?.email || 'N/A'"></span>
                                </div>

                                <div>
                                    <span class="text-white-dark block text-[11px] mb-0.5">Current Education:</span>
                                    <span class="font-bold text-dark dark:text-white-light" x-text="selectedLead?.current_education || 'N/A'"></span>
                                </div>

                                <div>
                                    <span class="text-white-dark block text-[11px] mb-0.5">Lead Contact Source:</span>
                                    <span class="badge badge-outline-primary" x-text="selectedLead?.source || 'Walk-in'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Preferences & Follow-up Date -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3 rounded-lg bg-white dark:bg-[#1b2e4b]">
                                <span class="text-white-dark block text-[10px] uppercase font-semibold mb-1">Preferred Country</span>
                                <p class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCountry?.name || 'N/A'"></p>
                            </div>
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3 rounded-lg bg-white dark:bg-[#1b2e4b]">
                                <span class="text-white-dark block text-[10px] uppercase font-semibold mb-1">Preferred Course</span>
                                <p class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCourse?.name || 'N/A'"></p>
                            </div>
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3 rounded-lg bg-white dark:bg-[#1b2e4b]">
                                <span class="text-white-dark block text-[10px] uppercase font-semibold mb-1">Follow-up Schedule</span>
                                <p class="font-bold" :class="isFollowUpPast ? 'text-danger' : 'text-slate-800 dark:text-slate-200'" x-text="formatDate(selectedLead?.next_follow_up_at)"></p>
                            </div>
                        </div>

                        <!-- Follow-up History Timeline -->
                        <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-4 rounded-xl text-xs space-y-3 bg-white dark:bg-[#1b2e4b]">
                            <div class="flex items-center justify-between">
                                <span class="text-white-dark block text-[10px] uppercase font-bold tracking-wider">Follow-up History & Activity Logs</span>
                                <span class="text-[11px] text-white-dark" x-text="followUpTimeline.length + ' record(s)'"></span>
                            </div>

                            <template x-if="followUpTimeline.length > 0">
                                <div class="space-y-2.5 max-h-48 overflow-y-auto">
                                    <template x-for="(fItem, idx) in followUpTimeline" :key="idx">
                                        <div class="p-2.5 rounded-lg border border-slate-200 dark:border-[#253b5c] bg-slate-50 dark:bg-[#121e32]">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="formatDate(fItem.date || fItem.follow_up_date)"></span>
                                                <span class="text-[10px] font-semibold text-slate-400" x-text="idx === 0 ? 'Latest' : 'Previous'"></span>
                                            </div>
                                            <p class="text-slate-600 dark:text-slate-300 text-[11px] whitespace-pre-wrap" x-text="fItem.notes || 'No note entered for this follow-up.'"></p>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="followUpTimeline.length === 0">
                                <div class="p-3 bg-slate-50 dark:bg-[#121e32] rounded-lg text-slate-400 text-center">
                                    No previous follow-up history recorded.
                                </div>
                            </template>
                        </div>

                        <!-- Internal Staff Notes -->
                        <div>
                            <span class="text-white-dark block text-[10px] uppercase font-bold tracking-wider mb-2">Internal Counselor Notes</span>
                            <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] p-3.5 rounded-xl text-xs leading-relaxed text-slate-700 dark:text-slate-300 max-h-36 overflow-y-auto bg-slate-50/50 dark:bg-[#121e32] whitespace-pre-wrap" 
                                 x-text="selectedLead?.notes || 'No internal notes saved for this lead.'">
                            </div>
                        </div>

                        <!-- System Metadata -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 bg-slate-50 dark:bg-[#121e32] p-3 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b] text-[11px]">
                            <div>
                                <span class="text-white-dark block">Collected By:</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="selectedCreator?.name || 'System'"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block">Created Date:</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="formatDate(selectedLead?.created_at)"></span>
                            </div>
                            <div>
                                <span class="text-white-dark block">Last Updated:</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="formatDate(selectedLead?.updated_at)"></span>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between border-t border-[#e0e6ed] bg-[#fbfbfb] px-6 py-3.5 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <button type="button" @click="leadModalOpen = false" class="btn btn-outline-danger btn-sm text-xs px-5">
                            Close
                        </button>
                        <div class="flex items-center gap-2">
                            <template x-if="selectedLead?.phone">
                                <a :href="whatsAppUrl" target="_blank" class="btn btn-outline-success btn-sm text-xs px-4 flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    <span>WhatsApp</span>
                                </a>
                            </template>
                            @can('*marketing')
                                <a :href="'{{ url('dashboard/marketing/leads') }}/' + (selectedLead?.id || '') + '/edit'" 
                                   class="btn btn-primary btn-sm text-xs px-5 flex items-center gap-1.5">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    <span>Edit Lead</span>
                                </a>
                            @endcan
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
    function leadsIndexHub() {
        return {
            leadModalOpen: false,
            selectedLead: null,
            selectedCountry: null,
            selectedCourse: null,
            selectedCreator: null,

            openLeadModal(lead, country, course, creator) {
                this.selectedLead = lead;
                this.selectedCountry = country;
                this.selectedCourse = course;
                this.selectedCreator = creator;
                this.leadModalOpen = true;
            },

            get cleanPhone() {
                if (!this.selectedLead?.phone) return '';
                return String(this.selectedLead.phone).replace(/[^0-9]/g, '');
            },

            get whatsAppUrl() {
                let phone = this.cleanPhone;
                if (!phone) return '#';
                if (phone.startsWith('01') && phone.length === 11) {
                    phone = '88' + phone;
                } else if (phone.startsWith('8801') && phone.length === 13) {
                    // already has country code
                }
                return 'https://wa.me/' + phone;
            },

            get priorityBadgeClass() {
                const p = String(this.selectedLead?.priority || 'low').toLowerCase();
                if (p === 'high') return 'badge-outline-danger';
                if (p === 'medium') return 'badge-outline-info';
                return 'badge-outline-secondary';
            },

            get statusBadgeClass() {
                const s = String(this.selectedLead?.status || 'pending').toLowerCase();
                if (s === 'interested') return 'badge-outline-success';
                if (s === 'forwarded') return 'badge-outline-info';
                if (s === 'rejected') return 'badge-outline-danger';
                return 'badge-outline-warning';
            },

            get isFollowUpPast() {
                if (!this.selectedLead?.next_follow_up_at) return false;
                const d = new Date(this.selectedLead.next_follow_up_at);
                return !isNaN(d.getTime()) && d < new Date();
            },

            get followUpTimeline() {
                if (!this.selectedLead?.follow_up_history) return [];
                if (Array.isArray(this.selectedLead.follow_up_history)) {
                    return this.selectedLead.follow_up_history.slice().reverse();
                }
                return [];
            },

            formatDate(dateStr) {
                if (!dateStr) return 'N/A';
                const d = new Date(dateStr);
                return isNaN(d.getTime()) ? dateStr : d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
        };
    }
</script>
@endpush
