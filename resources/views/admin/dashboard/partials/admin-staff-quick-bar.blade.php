{{-- Top Quick Access Staff Operations Switcher Bar for Admin & Super Admin --}}
<div class="mb-6 rounded-xl border border-[#e0e6ed] bg-white p-4 shadow-sm dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
    <div class="flex flex-wrap items-center justify-between gap-4">
        
        {{-- Left: Icon & Title --}}
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary dark:bg-primary/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-dark dark:text-white-light flex items-center gap-2">
                    <span>Staff Operations &amp; Daily Activities</span>
                    <span class="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold text-primary">Live Monitor</span>
                </h4>
                <p class="text-xs text-white-dark">Quickly inspect any staff member's daily performance, lead communications, and pending files.</p>
            </div>
        </div>

        {{-- Right: Dropdown & Jump to My Operations --}}
        @if(!empty($staffMembers) && $staffMembers->isNotEmpty())
            <form action="{{ route('tyro-dashboard.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="timeframe" value="{{ $timeframe ?? 'today' }}">
                
                <div class="relative min-w-[250px]">
                    <select name="user_id" onchange="this.form.submit()" class="form-select text-xs font-semibold py-1.5 text-slate-700 dark:text-slate-200" style="height: 38px; border-radius: 8px;">
                        <option value="">👥 Select staff member to inspect...</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ request('user_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <a href="#user-daily-operations-widget" class="btn btn-outline-primary btn-sm flex items-center gap-1.5 font-semibold text-xs py-2 px-3 shadow-none hover:shadow-sm" style="height: 38px; border-radius: 8px;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                    <span>My Operations (Bottom)</span>
                </a>
            </form>
        @endif

    </div>
</div>
