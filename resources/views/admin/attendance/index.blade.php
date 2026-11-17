@extends('admin.layouts.master')

@section('title', 'Staff Attendance Board')

@section('content')
<div x-data="attendanceIndexHandler()">
    
    <!-- Top Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase text-dark dark:text-white-light">Daily Attendance Board</h2>
            <p class="text-xs text-white-dark mt-1">Review live check-in logs, geofence distances, IP tracking, and adjust staff records.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="openManualModal()" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Record Manual Attendance
            </button>
            <a href="{{ route('admin.attendance.settings') }}" class="btn btn-outline-secondary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Settings
            </a>
            <a href="{{ route('admin.attendance.report') }}" class="btn btn-outline-info gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10M18 20V4M6 20v-4"/></svg>
                Monthly Deductions
            </a>
        </div>
    </div>

    <!-- Filter & Date Bar -->
    <div class="panel mt-6 p-4">
        <!-- Top Row: Date Presets & Quick Stats -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-3 border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-dark dark:text-white-light uppercase tracking-wider">Quick Date:</span>
                <a href="{{ route('admin.attendance.index', array_merge(request()->except('date'), ['date' => now()->toDateString()])) }}" 
                   class="btn btn-xs {{ $date === now()->toDateString() ? 'btn-primary' : 'btn-outline-primary' }}">
                    Today
                </a>
                <a href="{{ route('admin.attendance.index', array_merge(request()->except('date'), ['date' => now()->subDay()->toDateString()])) }}" 
                   class="btn btn-xs {{ $date === now()->subDay()->toDateString() ? 'btn-primary' : 'btn-outline-primary' }}">
                    Yesterday
                </a>
            </div>

            <!-- Quick Day Stats -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="badge bg-primary/10 text-primary border border-primary/20 text-xs px-2.5 py-1 font-semibold">Staff: {{ $counts['total_staff'] }}</span>
                <span class="badge bg-success/10 text-success border border-success/20 text-xs px-2.5 py-1 font-semibold">Present: {{ $counts['present'] }}</span>
                <span class="badge bg-warning/10 text-warning border border-warning/20 text-xs px-2.5 py-1 font-semibold">Late: {{ $counts['late'] }}</span>
                <span class="badge bg-info/10 text-info border border-info/20 text-xs px-2.5 py-1 font-semibold">Half Day: {{ $counts['half_day'] }}</span>
                <span class="badge bg-danger/10 text-danger border border-danger/20 text-xs px-2.5 py-1 font-semibold">Absent: {{ $counts['absent'] }}</span>
                @if(($counts['not_checked_in'] ?? 0) > 0)
                    <span class="badge bg-dark/10 text-dark dark:text-white-light border border-dark/20 text-xs px-2.5 py-1 font-semibold">Not Checked In: {{ $counts['not_checked_in'] }}</span>
                @endif
            </div>
        </div>

        <!-- Filter Controls Row -->
        <form action="{{ route('admin.attendance.index') }}" method="GET" style="display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap; width: 100%;">
            <!-- Date Picker -->
            <div style="flex: 1; min-width: 140px;">
                <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Date</label>
                <input type="date" name="date" value="{{ $date }}" class="form-input text-xs w-full" style="height: 38px;" />
            </div>

            <!-- Staff Member Dropdown -->
            <div style="flex: 1.5; min-width: 180px;">
                <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Staff Member</label>
                <select name="user_id" class="form-select text-xs w-full" style="height: 38px;">
                    <option value="">All Staff Members</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Dropdown -->
            <div style="flex: 1; min-width: 140px;">
                <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Status</label>
                <select name="status" class="form-select text-xs w-full" style="height: 38px;">
                    <option value="">All Statuses</option>
                    <option value="present" {{ ($status ?? '') === 'present' ? 'selected' : '' }}>Present (On Time)</option>
                    <option value="late" {{ ($status ?? '') === 'late' ? 'selected' : '' }}>Late</option>
                    <option value="half_day" {{ ($status ?? '') === 'half_day' ? 'selected' : '' }}>Half Day</option>
                    <option value="absent" {{ ($status ?? '') === 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="leave" {{ ($status ?? '') === 'leave' ? 'selected' : '' }}>Leave</option>
                </select>
            </div>

            <!-- Keyword Search -->
            <div style="flex: 1.5; min-width: 180px;">
                <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-300">Keyword Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, IP..." class="form-input text-xs w-full" style="height: 38px;" />
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 8px; align-items: flex-end;">
                <button type="submit" class="btn btn-primary text-xs" style="height: 38px; padding: 0 18px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Filter
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-danger text-xs" style="height: 38px; padding: 0 16px; display: inline-flex; align-items: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="panel mt-6">
        <div class="datatable">
            <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Status</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Hours</th>
                            <th>Geofence Distance</th>
                            <th>IP Address</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>
                                    <div class="font-bold text-dark dark:text-white-light">{{ $attendance->user->name ?? 'Deleted User' }}</div>
                                    <div class="text-xs text-white-dark">{{ $attendance->user->email ?? '' }}</div>
                                    @if($attendance->is_manually_adjusted)
                                        <span class="text-[10px] text-info font-semibold inline-block mt-0.5" title="Adjusted by {{ $attendance->adjuster->name ?? 'Admin' }}: {{ $attendance->adjustment_reason }}">
                                            * Adjusted
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-{{ $attendance->status_color }}/10 text-{{ $attendance->status_color }} border border-{{ $attendance->status_color }}/20 text-xs font-semibold">
                                        {{ $attendance->status_label }}
                                    </span>
                                    @if($attendance->is_late && $attendance->late_minutes > 0)
                                        <div class="text-[10px] text-danger font-semibold mt-0.5">+{{ $attendance->late_minutes }}m late</div>
                                    @endif
                                </td>

                                <td>
                                    <div class="text-sm font-semibold">{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '-' }}</div>
                                </td>

                                <td>
                                    <div class="text-sm font-semibold">{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : ($attendance->check_in ? 'In Progress' : '-') }}</div>
                                </td>

                                <td>
                                    <span class="font-mono text-sm font-bold text-primary">{{ $attendance->total_hours > 0 ? $attendance->total_hours . ' hrs' : '-' }}</span>
                                </td>

                                <td>
                                    @if($attendance->check_in_distance_meters !== null)
                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-mono {{ $attendance->check_in_distance_meters > 200 ? 'text-warning font-bold' : 'text-success' }}">
                                                    {{ $attendance->check_in_distance_meters }}m
                                                </span>
                                                @if($attendance->check_in_lat && $attendance->check_in_lng)
                                                    <a href="https://maps.google.com/?q={{ $attendance->check_in_lat }},{{ $attendance->check_in_lng }}" 
                                                       target="_blank" 
                                                       class="text-primary hover:underline text-xs" 
                                                       title="View coordinates on Google Maps">
                                                        📍 Map
                                                    </a>
                                                @endif
                                            </div>
                                            @if($attendance->office_name)
                                                <div class="text-[10px] text-white-dark font-medium truncate max-w-[160px]" title="{{ $attendance->office_name }}">
                                                    🏢 {{ $attendance->office_name }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-white-dark">-</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="text-xs font-mono text-white-dark">{{ $attendance->check_in_ip ?? '-' }}</div>
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
                                                        @click="openAdjustModal(@js($attendance)); toggle();" 
                                                        class="flex w-full items-center px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-dark/40 text-primary">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    Adjust / Override
                                                </button>
                                            </li>
                                            @if($attendance->check_in_lat && $attendance->check_in_lng)
                                            <li>
                                                <a href="https://maps.google.com/?q={{ $attendance->check_in_lat }},{{ $attendance->check_in_lng }}" 
                                                   target="_blank" 
                                                   @click="toggle()" 
                                                   class="flex w-full items-center px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-dark/40 text-info">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                                                    Open GPS on Maps
                                                </a>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-white-dark">No attendance logs recorded for this date.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>

    <!-- Manual Attendance Entry Modal -->
    <template x-teleport="#modal-portal">
        <div x-cloak x-show="manualModalOpen" class="modal-overlay-full" style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="manualModalOpen = false"
                     x-show="manualModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light">Record Manual Attendance</h5>
                        <button type="button" @click="manualModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white">✕</button>
                    </div>

                    <form action="{{ route('admin.attendance.manual-store') }}" method="POST" class="p-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold mb-1">Select Staff Member</label>
                            <select name="user_id" class="form-select text-sm" required>
                                <option value="">Choose Staff Member</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold mb-1">Date</label>
                                <input type="date" name="date" value="{{ $date }}" class="form-input text-sm" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1">Status</label>
                                <select name="status" class="form-select text-sm" required>
                                    <option value="present">Present (On Time)</option>
                                    <option value="late">Late</option>
                                    <option value="half_day">Half Day</option>
                                    <option value="absent">Absent</option>
                                    <option value="leave">Approved Leave</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold mb-1">Check-In Time</label>
                                <input type="time" name="check_in_time" value="09:30" class="form-input text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1">Check-Out Time</label>
                                <input type="time" name="check_out_time" value="18:30" class="form-input text-sm" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1">Reason / Note</label>
                            <textarea name="notes" rows="2" class="form-textarea text-xs" placeholder="e.g. Employee forgot phone or mobile GPS glitch..."></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                            <button type="button" @click="manualModalOpen = false" class="btn btn-outline-danger text-xs">Cancel</button>
                            <button type="submit" class="btn btn-primary text-xs">Save Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Adjust / Override Modal -->
    <template x-teleport="#modal-portal">
        <div x-cloak x-show="adjustModalOpen" class="modal-overlay-full" style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="adjustModalOpen = false"
                     x-show="adjustModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light">Adjust Staff Attendance</h5>
                        <button type="button" @click="adjustModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white">✕</button>
                    </div>

                    <form :action="'{{ url('dashboard/attendance') }}/' + (activeAtt ? activeAtt.id : '') + '/adjust'" method="POST" class="p-5 space-y-4">
                        @csrf
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-dark text-xs">
                            <span class="text-white-dark">Staff:</span> <strong x-text="activeAtt && activeAtt.user ? activeAtt.user.name : ''"></strong><br/>
                            <span class="text-white-dark">Date:</span> <span x-text="activeAtt ? activeAtt.date : ''"></span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1">New Status</label>
                            <select name="status" x-model="adjustStatus" class="form-select text-sm" required>
                                <option value="present">Present (On Time / Excused)</option>
                                <option value="late">Late</option>
                                <option value="half_day">Half Day</option>
                                <option value="absent">Absent</option>
                                <option value="leave">Approved Leave</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold mb-1">Check-In Time</label>
                                <input type="time" name="check_in_time" x-model="adjustInTime" class="form-input text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1">Check-Out Time</label>
                                <input type="time" name="check_out_time" x-model="adjustOutTime" class="form-input text-sm" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1">Reason for Adjustment (Required)</label>
                            <input type="text" name="adjustment_reason" class="form-input text-sm" placeholder="e.g. Excused late due to traffic, or corrected shift time..." required />
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                            <button type="button" @click="adjustModalOpen = false" class="btn btn-outline-danger text-xs">Cancel</button>
                            <button type="submit" class="btn btn-primary text-xs">Apply Override</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('attendanceIndexHandler', () => ({
        manualModalOpen: false,
        adjustModalOpen: false,
        activeAtt: null,
        adjustStatus: 'present',
        adjustInTime: '',
        adjustOutTime: '',

        openManualModal() {
            this.manualModalOpen = true;
        },

        openAdjustModal(att) {
            this.activeAtt = att;
            this.adjustStatus = att.status;
            this.adjustInTime = att.check_in ? att.check_in.substring(11, 16) : '';
            this.adjustOutTime = att.check_out ? att.check_out.substring(11, 16) : '';
            this.adjustModalOpen = true;
        }
    }));
});
</script>
@endsection
