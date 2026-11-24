@php
    $cards = $userOperations['cards'] ?? [];
    $queues = $userOperations['queues'] ?? [];
    $currentTimeframe = $timeframe ?? 'today';
    $viewingName = $viewingUser->name ?? 'User';
@endphp

<!-- User Operations Dashboard Main Container -->
<div id="user-daily-operations-widget" class="panel p-5 bg-white dark:bg-[#1b2e4b] rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] mb-6 shadow-sm">

    <!-- Admin User Inspection Banner (If an Admin is inspecting another user's dashboard) -->
    @if(!empty($isInspectingOther))
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-amber-300 bg-amber-50 p-3 text-amber-900 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-200">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-200 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </span>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider">Admin Overview Mode:</span>
                    <span class="text-xs font-semibold">Viewing daily operations for <strong>{{ $viewingName }}</strong></span>
                </div>
            </div>
            <a href="{{ route('tyro-dashboard.index', ['timeframe' => $currentTimeframe]) }}" class="btn btn-xs btn-outline-danger font-semibold">
                Return to My Dashboard
            </a>
        </div>
    @endif

    <!-- Top Toolbar: Title, Timeframe Pills, and Admin Staff Switcher -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e0e6ed] pb-4 dark:border-[#1b2e4b]">
        
        <!-- Left: Heading -->
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary dark:bg-primary/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h4 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2">
                    <span>{{ !empty($isInspectingOther) ? "{$viewingName}'s Operations Dashboard" : "My Daily Operations Dashboard" }}</span>
                    <span class="rounded bg-primary/10 px-2 py-0.5 text-[11px] font-bold text-primary">Live</span>
                </h4>
                <p class="text-xs text-white-dark mt-0.5">Real-time daily doings, lead communications, pipeline statuses &amp; collections.</p>
            </div>
        </div>

        <!-- Right: Timeframe Filter + Admin Staff Switcher -->
        <div class="flex flex-wrap items-center gap-3">
            
            <!-- Timeframe Filter Pills (Matching Filter Widget Outline Style) -->
            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                @php
                    $timeframeOptions = [
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'this_month' => 'This Month',
                        'all_time' => 'All Time',
                    ];
                @endphp
                @foreach($timeframeOptions as $key => $label)
                    <a href="{{ route('tyro-dashboard.index', array_merge(request()->except('timeframe'), ['timeframe' => $key])) }}" 
                       style="height: 34px; border-radius: 6px; padding: 0 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; {{ $currentTimeframe === $key ? 'background-color: #4361ee; color: #ffffff; border: 1px solid #4361ee; box-shadow: 0 2px 4px rgba(67, 97, 238, 0.25);' : 'background-color: #ffffff; color: #475569; border: 1px solid #e0e6ed;' }}"
                       class="hover:border-primary hover:text-primary dark:bg-[#121e32] dark:border-[#2e405b] dark:text-slate-300">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Admin Staff Switcher Dropdown (Visible only to Admin / Super Admin) -->
            @if(!empty($isAdmin) && $staffMembers->isNotEmpty())
                <div class="flex items-center gap-1.5" x-data="{ openStaffSelect: false }">
                    <form action="{{ route('tyro-dashboard.index') }}" method="GET" class="flex items-center gap-1.5">
                        <input type="hidden" name="timeframe" value="{{ $currentTimeframe }}">
                        <select name="user_id" onchange="this.form.submit()" class="form-select text-xs py-1 font-semibold text-slate-700 dark:text-slate-200" style="height: 34px; width: 190px; border-radius: 6px;">
                            <option value="">👤 My Own Dashboard</option>
                            <optgroup label="Inspect Staff Member">
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ ($viewingUser->id ?? 0) === $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </form>
                </div>
            @endif

        </div>

    </div>

    <!-- 10 Operational KPI Cards Grid (Matches Pipeline Intelligence Card Styling: Colored Left Border, Clean Text & Numbers) -->
    <div class="ops-kpi-grid">

        <!-- Card 1: New Leads -->
        @php $c = $cards['new_leads'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #2563eb; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        New Leads
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 2: Calls Logged -->
        @php $c = $cards['new_calls'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #06b6d4; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        Calls Logged
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 3: Numbers Collected -->
        @php $c = $cards['numbers_collected'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #14b8a6; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        Numbers Collected
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 4: Follow-ups Due -->
        @php $c = $cards['follow_ups'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #f59e0b; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        Follow-ups Due
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white {{ $c['count'] > 0 ? 'text-amber-600 dark:text-amber-400' : '' }}" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 5: Appointments -->
        @php $c = $cards['appointments'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #8b5cf6; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        Appointments
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 6: OL Pending -->
        @php $c = $cards['ol_pending'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #f97316; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        OL Pending
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 7: Final Files -->
        @php $c = $cards['final_files'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #6366f1; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        Final Files
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 8: Completed -->
        @php $c = $cards['completed'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #10b981; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        Completed
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 9: Payment Collected -->
        @php $c = $cards['payments'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #16a34a; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        Payment Collected
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-xl font-bold text-slate-900 dark:text-white truncate" style="margin-top: 8px; line-height: 1.2;">
                    {{ $c['count'] }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['subtitle'] ?? 'Collections' }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

        <!-- Card 10: Active Students -->
        @php $c = $cards['active_students'] ?? null; @endphp
        @if($c)
            <a href="{{ $c['route'] }}"
               class="group transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
               style="display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; border-left: 4px solid #4f46e5; padding: 14px 16px; text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-primary transition truncate">
                        Active Students
                    </span>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-primary transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white" style="margin-top: 8px; line-height: 1.2;">
                    {{ number_format($c['count']) }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span class="truncate">{{ $c['title'] }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition text-primary font-bold">&rarr;</span>
                </div>
            </a>
        @endif

    </div>

    <!-- Daily Action Queues (Urgent Follow-ups & Pending Offer Letters) -->
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">

        <!-- Queue 1: Urgent Follow-ups for this user -->
        <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; padding: 16px;" class="dark:bg-[#1b2e4b] dark:border-[#2e405b]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#e0e6ed] dark:border-[#2e405b]">
                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500 inline-block"></span>
                    Scheduled Follow-ups Queue
                </h5>
                <a href="{{ route('admin.marketing.leads.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-primary hover:underline">
                    View All Leads &rarr;
                </a>
            </div>

            @php $followUps = $queues['follow_ups'] ?? collect(); @endphp
            @if($followUps->isEmpty())
                <div class="py-6 text-center text-xs text-white-dark">
                    No urgent follow-ups scheduled for this period. Great job!
                </div>
            @else
                <div class="space-y-2">
                    @foreach($followUps as $lead)
                        <div class="flex items-center justify-between gap-2 rounded-md p-2.5 text-xs transition" style="background: #f8fafc; border: 1px solid #e2e8f0;" class="dark:bg-[#121e32] dark:border-[#1b2e4b]">
                            <div>
                                <a href="{{ route('admin.marketing.leads.show', $lead->id) }}" class="font-bold text-dark dark:text-white-light hover:text-primary transition">
                                    {{ $lead->student_name }}
                                </a>
                                <div class="text-[11px] text-white-dark mt-0.5">
                                    {{ $lead->phone ?? 'No phone' }} | {{ $lead->preferred_country ?? 'Any' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() ? 'bg-danger/10 text-danger' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' }} text-[10px] font-bold">
                                    {{ $lead->next_follow_up_at ? $lead->next_follow_up_at->format('d M h:i A') : 'Pending' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Queue 2: Applications Awaiting Offer Letter (OL Pending) -->
        <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e0e6ed; padding: 16px;" class="dark:bg-[#1b2e4b] dark:border-[#2e405b]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#e0e6ed] dark:border-[#2e405b]">
                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-orange-500 inline-block"></span>
                    Applications Awaiting Offer Letter
                </h5>
                <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-primary hover:underline">
                    View All Apps &rarr;
                </a>
            </div>

            @php $pendingOl = $queues['pending_ol'] ?? collect(); @endphp
            @if($pendingOl->isEmpty())
                <div class="py-6 text-center text-xs text-white-dark">
                    No pending offer letters awaiting response in this portfolio.
                </div>
            @else
                <div class="space-y-2">
                    @foreach($pendingOl as $app)
                        <div class="flex items-center justify-between gap-2 rounded-md p-2.5 text-xs transition" style="background: #f8fafc; border: 1px solid #e2e8f0;" class="dark:bg-[#121e32] dark:border-[#1b2e4b]">
                            <div>
                                <a href="{{ route('admin.applications.show', $app->id) }}" class="font-bold text-dark dark:text-white-light hover:text-primary transition">
                                    {{ $app->student->full_name ?? 'Student' }}
                                </a>
                                <div class="text-[11px] text-white-dark truncate max-w-[220px] mt-0.5">
                                    {{ $app->university->name ?? 'University' }} ({{ $app->course->name ?? 'Course' }})
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300 text-[10px] font-bold">
                                    Applied {{ $app->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>

{{-- Styles for Operational KPI Grid & Dark Mode Support --}}
<style>
    .ops-kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    @media (max-width: 1280px) {
        .ops-kpi-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    @media (max-width: 768px) {
        .ops-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 480px) {
        .ops-kpi-grid {
            grid-template-columns: 1fr;
        }
    }
    .dark [style*="background: #ffffff"] {
        background: #1b2e4b !important;
    }
    .dark [style*="background: #f8fafc"] {
        background: #121e32 !important;
    }
    .dark [style*="border: 1px solid #e0e6ed"] {
        border-color: #2e405b !important;
    }
    .dark [style*="border: 1px solid #e2e8f0"] {
        border-color: #1b2e4b !important;
    }
</style>
