@if(auth()->check() && !auth()->user()->todayAttendance)
@php
    $settings = \App\Models\AttendanceSetting::getSettings();
@endphp

<script>
function attendancePromptModal() {
    return {
        promptOpen: true,
        loading: false,
        errorMessage: '',
        currentTime: '',

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            try {
                sessionStorage.removeItem('insaf_attendance_prompt_dismissed');
            } catch(e) {}
        },

        closeModal() {
            this.promptOpen = false;
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
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
                        try {
                            sessionStorage.removeItem('insaf_attendance_prompt_dismissed');
                        } catch(e) {}
                        window.location.reload();
                    } else {
                        this.errorMessage = data.message || 'Check-in failed. Please ensure you are within office premises.';
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
                        sendPayload(null, null);
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            } else {
                sendPayload(null, null);
            }
        }
    };
}
</script>

<div x-data="attendancePromptModal()">
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="promptOpen"
             class="modal-overlay-full">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="closeModal()"
                     x-show="promptOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-md rounded-2xl border border-[#e0e6ed] bg-white shadow-2xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <!-- Top Gradient Line -->
                    <div class="h-1.5 w-full bg-gradient-to-r from-primary to-success"></div>

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <div>
                            <h5 class="text-base font-bold text-dark dark:text-white-light">
                                Daily Attendance Check-In
                            </h5>
                            <p class="text-[11px] text-white-dark mt-0.5">Welcome, {{ explode(' ', auth()->user()->name)[0] }}! Please mark your attendance.</p>
                        </div>
                        <button type="button" @click="closeModal()" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4 text-xs">
                        
                        <!-- Shift Info Card -->
                        <div class="bg-slate-50 dark:bg-dark/40 rounded-xl p-3.5 border border-slate-100 dark:border-dark space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-white-dark">Office Shift:</span>
                                <span class="font-bold text-dark dark:text-white-light">
                                    {{ \Carbon\Carbon::parse($settings->shift_start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($settings->shift_end_time)->format('h:i A') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-white-dark">Grace Period:</span>
                                <span class="font-semibold text-success">+{{ $settings->late_grace_minutes }} minutes</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-white-dark">Work Location:</span>
                                <span class="font-semibold text-dark dark:text-white-light truncate max-w-[200px]">{{ $settings->office_name }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-1 border-t border-slate-200 dark:border-dark">
                                <span class="text-white-dark">Current Time:</span>
                                <span class="font-mono font-bold text-primary" x-text="currentTime">{{ now()->format('h:i:s A') }}</span>
                            </div>
                        </div>

                        <!-- Error notice if geolocation or IP check fails -->
                        <div x-cloak x-show="errorMessage" class="text-xs text-danger font-semibold flex items-start gap-1.5 p-3 rounded-lg bg-danger/10 border border-danger/20">
                            <svg class="h-4 w-4 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span x-text="errorMessage"></span>
                        </div>

                        <!-- Check In Action Button -->
                        <div>
                            <button type="button" 
                                    @click="submitCheckIn()" 
                                    :disabled="loading" 
                                    class="btn btn-success w-full py-2.5 text-sm font-bold shadow-md hover:shadow-lg transition-all rounded-lg flex items-center justify-center">
                                <span x-show="loading" class="animate-spin inline-block h-4 w-4 border-2 border-white border-l-transparent rounded-full mr-2"></span>
                                <span x-text="loading ? 'Verifying GPS & Location...' : 'Check In Now'"></span>
                            </button>
                            <div class="text-[10px] text-white-dark text-center mt-2 flex items-center justify-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                                <span>Verifies office geofence & network IP automatically</span>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between border-t border-[#e0e6ed] bg-[#fbfbfb] px-6 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <button type="button" @click="closeModal()" class="text-xs text-white-dark hover:text-dark dark:hover:text-white transition">
                            Remind Me Later
                        </button>
                        <a href="{{ route('admin.attendance.my-attendance') }}" class="text-xs font-semibold text-primary hover:underline">
                            View Attendance History &rarr;
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </template>
</div>
@endif
