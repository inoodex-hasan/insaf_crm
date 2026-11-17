@php
    $user = auth()->user();
    $att = $todayAttendance ?? ($user ? $user->todayAttendance : null);
    $settings = $attendanceSettings ?? \App\Models\AttendanceSetting::getSettings();
@endphp

@if(auth()->check())
<div class="panel mb-6 border border-[#e0e6ed] bg-white dark:border-[#1b2e4b] dark:bg-[#1b2e4b] rounded-xl shadow-sm overflow-hidden p-0" 
     x-data="attendanceTracker()">
    
    <!-- Top Status Indicator Bar -->
    <div class="h-1.5 w-full {{ $att && $att->check_out ? 'bg-gradient-to-r from-info to-success' : ($att ? 'bg-gradient-to-r from-success to-primary' : 'bg-gradient-to-r from-warning to-danger') }}"></div>

    <!-- Main Widget Body -->
    <div style="padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        
        <!-- Left: Status & Shift Details -->
        <div style="flex: 2; min-width: 300px;">
            <!-- Title & Status Badge -->
            <div class="flex flex-wrap items-center gap-2">
                <h4 class="text-base font-bold text-dark dark:text-white-light">Work Shift & Attendance</h4>
                
                @if(!$att)
                    <span class="badge bg-danger/10 text-danger border border-danger/20 text-xs font-bold py-1 px-2.5 rounded-full inline-flex items-center" style="gap: 4px;">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-danger opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-danger"></span>
                        </span>
                        <span>Not Checked In Today</span>
                    </span>
                @elseif($att && !$att->check_out)
                    <span class="badge bg-{{ $att->status_color }}/10 text-{{ $att->status_color }} border border-{{ $att->status_color }}/20 text-xs font-bold py-1 px-2.5 rounded-full inline-flex items-center" style="gap: 4px;">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-{{ $att->status_color }} opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-{{ $att->status_color }}"></span>
                        </span>
                        <span>Checked In ({{ $att->status_label }})</span>
                    </span>
                @else
                    <span class="badge bg-success/10 text-success border border-success/20 text-xs font-bold py-1 px-2.5 rounded-full inline-flex items-center">
                        Shift Completed ({{ $att->total_hours }} hrs)
                    </span>
                @endif
            </div>

            <!-- Shift Metadata Tags with Clean Spacing -->
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 8px;">
                <span class="inline-flex items-center text-[11px] font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-dark/50 px-2.5 py-1 rounded-md border border-slate-200 dark:border-dark">
                    Shift: {{ \Carbon\Carbon::parse($settings->shift_start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($settings->shift_end_time)->format('h:i A') }}
                </span>
                <span class="inline-flex items-center text-[11px] font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-dark/50 px-2.5 py-1 rounded-md border border-slate-200 dark:border-dark">
                    Grace: +{{ $settings->late_grace_minutes }} mins
                </span>
                <span class="inline-flex items-center text-[11px] font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-dark/50 px-2.5 py-1 rounded-md border border-slate-200 dark:border-dark">
                    Office: {{ $att->office_name ?? $settings->office_name }}
                </span>
                @if($settings->enforce_geofence)
                    <span class="inline-flex items-center text-[11px] font-semibold text-info bg-info/10 px-2.5 py-1 rounded-md border border-info/20">
                        Geofence: {{ $settings->geofence_radius_meters }}m
                    </span>
                @endif
            </div>

            <!-- Today Timestamps Details (If Checked In) -->
            @if($att)
                <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-top: 8px;" class="text-xs">
                    <span class="text-dark dark:text-white-light font-medium">
                        Check-In: <strong>{{ $att->check_in?->format('h:i A') ?? '-' }}</strong>
                    </span>
                    @if($att->check_out)
                        <span class="text-dark dark:text-white-light font-medium">
                            Check-Out: <strong>{{ $att->check_out->format('h:i A') }}</strong>
                        </span>
                        <span class="font-bold text-success">
                            Total: {{ $att->total_hours }} hrs
                        </span>
                    @else
                        <span class="font-bold text-primary inline-flex items-center" style="gap: 4px;">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-primary"></span>
                            </span>
                            <span>Active Shift: <span x-text="elapsedTime">--</span></span>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Middle: Live Digital Clock -->
        <div class="hidden lg:flex" style="flex-direction: column; align-items: center; justify-content: center; padding: 0 24px; border-left: 1px solid rgba(0,0,0,0.08); border-right: 1px solid rgba(0,0,0,0.08); min-width: 180px;">
            <div class="font-mono font-black text-2xl text-dark dark:text-white-light tracking-wider" x-text="currentTime">
                {{ now()->format('h:i:s A') }}
            </div>
            <div class="text-[11px] text-white-dark font-medium mt-0.5">
                {{ now()->format('l, M d, Y') }}
            </div>
            <span class="badge bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded mt-1.5">
                Live Clock
            </span>
        </div>

        <!-- Right: Primary Action -->
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; min-width: 180px;">
            <div class="flex items-center gap-2">
                @if(!$att)
                    <button type="button" 
                            @click="submitCheckIn()" 
                            :disabled="loading" 
                            class="btn btn-success px-6 py-2.5 font-bold shadow-md hover:shadow-lg transition-all rounded-lg text-sm"
                            style="min-width: 170px; justify-content: center;">
                        <span x-show="loading" class="animate-spin inline-block h-4 w-4 border-2 border-white border-l-transparent rounded-full mr-2"></span>
                        <span x-text="loading ? 'Verifying GPS...' : 'Check In Now'"></span>
                    </button>
                @elseif(!$att->check_out)
                    <button type="button" 
                            @click="submitCheckOut()" 
                            :disabled="loading" 
                            class="btn btn-primary px-6 py-2.5 font-bold shadow-md hover:shadow-lg transition-all rounded-lg text-sm"
                            style="min-width: 170px; justify-content: center;">
                        <span x-show="loading" class="animate-spin inline-block h-4 w-4 border-2 border-white border-l-transparent rounded-full mr-2"></span>
                        <span x-text="loading ? 'Recording...' : 'Check Out Now'"></span>
                    </button>
                @else
                    <div class="px-5 py-2.5 rounded-lg bg-success/10 text-success border border-success/20 text-xs font-bold flex items-center justify-center" 
                         style="min-width: 170px;">
                        Shift Done Today
                    </div>
                @endif

                @if(!request()->routeIs('admin.attendance.my-attendance'))
                    <a href="{{ route('admin.attendance.my-attendance') }}" 
                       class="btn btn-outline-secondary p-2.5 rounded-lg" 
                       title="View My Attendance Log">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </a>
                @endif
            </div>

            <!-- <div class="text-[10px] text-white-dark flex items-center gap-1.5 mt-0.5">
                <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                <span>Location & IP Verified</span>
            </div> -->
        </div>

    </div>

    <!-- Error / Policy Alert Banner -->
    <div x-cloak x-show="errorMessage" class="border-t border-danger/20 bg-danger/5 px-6 py-2.5 flex items-center justify-between text-xs text-danger font-semibold">
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span x-text="errorMessage"></span>
        </div>
        <button type="button" @click="errorMessage = ''" class="text-danger hover:underline text-[11px]">Dismiss</button>
    </div>
</div>

<script>
function attendanceTracker() {
    return {
        loading: false,
        errorMessage: '',
        currentTime: '',
        elapsedTime: '',
        checkInTime: @js($att && $att->check_in ? $att->check_in->toISOString() : null),

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            if (this.checkInTime) {
                this.updateElapsed();
                setInterval(() => this.updateElapsed(), 10000);
            }
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        },

        updateElapsed() {
            if (!this.checkInTime) return;
            const checkIn = new Date(this.checkInTime);
            const now = new Date();
            const diffMs = now - checkIn;
            if (diffMs > 0) {
                const hours = Math.floor(diffMs / (1000 * 60 * 60));
                const mins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                this.elapsedTime = `${hours}h ${mins}m`;
            }
        },

        async submitCheckIn() {
            this.loading = true;
            this.errorMessage = '';

            const sendPayload = (lat = null, lng = null) => {
                fetch('{{ route('admin.attendance.check-in') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                })
                .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
                .then(({ ok, data }) => {
                    this.loading = false;
                    if (ok && data.success) {
                        window.location.reload();
                    } else {
                        this.errorMessage = data.message || 'Check-in failed.';
                    }
                })
                .catch(err => {
                    this.loading = false;
                    this.errorMessage = 'Network error during check-in: ' + err.message;
                });
            };

            // Request Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        sendPayload(pos.coords.latitude, pos.coords.longitude);
                    },
                    err => {
                        console.warn('Geolocation error:', err);
                        // Send null coordinates if browser blocks GPS or fails; backend validates against policy
                        sendPayload(null, null);
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            } else {
                sendPayload(null, null);
            }
        },

        async submitCheckOut() {
            this.loading = true;
            this.errorMessage = '';

            const sendPayload = (lat = null, lng = null) => {
                fetch('{{ route('admin.attendance.check-out') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                })
                .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
                .then(({ ok, data }) => {
                    this.loading = false;
                    if (ok && data.success) {
                        window.location.reload();
                    } else {
                        this.errorMessage = data.message || 'Check-out failed.';
                    }
                })
                .catch(err => {
                    this.loading = false;
                    this.errorMessage = 'Network error during check-out: ' + err.message;
                });
            };

            // Request Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => sendPayload(pos.coords.latitude, pos.coords.longitude),
                    () => sendPayload(null, null),
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            } else {
                sendPayload(null, null);
            }
        }
    };
}
</script>
@endif
