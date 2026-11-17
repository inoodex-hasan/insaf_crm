<!-- start header section -->
<header class="z-40">
    <div class="shadow-sm">
        <div class="relative flex w-full items-center bg-white px-5 py-2.5 dark:bg-[#0e1726]">
            <div class="horizontal-logo flex items-center justify-between ltr:mr-2 rtl:ml-2 lg:hidden">
                <a href="{{ route('tyro-dashboard.index') }}" class="main-logo flex shrink-0 items-center">
                    <img class="inline w-8 ltr:-ml-1 rtl:-mr-1"
                        src="{{ get_setting('app_logo') ? asset('storage/' . get_setting('app_logo')) : asset('assets/images/logo.svg') }}"
                        alt="logo" />
                    <span
                        class="hidden align-middle text-2xl font-semibold transition-all duration-300 ltr:ml-1.5 rtl:mr-1.5 dark:text-white-light md:inline">{{ get_setting('app_name', config('app.name')) }}</span>
                </a>

                <a href="javascript:;"
                    class="collapse-icon flex flex-none rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary ltr:ml-2 rtl:mr-2 dark:bg-dark/40 dark:text-[#d0d2d6] dark:hover:bg-dark/60 dark:hover:text-primary lg:hidden"
                    @click="$store.app.toggleSidebar()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 7L4 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path opacity="0.5" d="M20 12L4 12" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" />
                        <path d="M20 17L4 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </a>
            </div>

            <div class="hidden ltr:mr-2 rtl:ml-2 sm:block">
                <ul class="flex items-center space-x-2 rtl:space-x-reverse dark:text-[#d0d2d6]">
                    <li>
                        <a href="{{ route('tyro-dashboard.index') }}"
                            class="block rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5"
                                    d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                    fill="currentColor" />
                                <path
                                    d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z"
                                    fill="currentColor" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex items-center space-x-1.5 ltr:ml-auto rtl:mr-auto rtl:space-x-reverse dark:text-[#d0d2d6] sm:flex-1 ltr:sm:ml-0 sm:rtl:mr-0 lg:space-x-2">
                <div class="sm:ltr:mr-auto sm:rtl:ml-auto w-full max-w-[500px]">
                    @if (isset($activeCurrencies) && $activeCurrencies->count() > 0)
                        <div class="relative flex h-10 items-center overflow-hidden px-4" 
                             x-data="{
                                active: 0,
                                count: {{ $activeCurrencies->count() }},
                                paused: false,
                                init() {
                                    setInterval(() => {
                                        if (!this.paused) {
                                            this.active = (this.active + 1) % this.count;
                                        }
                                    }, 4000)
                                }
                             }"
                             @mouseenter="paused = true"
                             @mouseleave="paused = false">
                            @foreach ($activeCurrencies as $index => $currency)
                                <div class="absolute inset-0 flex items-center justify-center px-4 transition-all duration-700 ease-in-out whitespace-nowrap"
                                     x-show="active === {{ $index }}"
                                     x-transition:enter="transition ease-out duration-700"
                                     x-transition:enter-start="opacity-0 translate-y-6"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-700 absolute"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-6">
                                    
                                    <div class="flex items-center space-x-4 text-sm rtl:space-x-reverse">
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                            <span class="rounded bg-primary px-1.5 py-0.5 text-[10px] font-black text-white uppercase">{{ $currency->code }}</span>
                                            <!-- <span class="font-bold text-gray-800 dark:text-white-light">{{ $currency->name }}</span> -->
                                        </div>
                                        <span class="h-1 w-1 rounded-full bg-primary/30"></span>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-lg font-bold text-gray-500">1 {{ $currency->code }} =</span>
                                            <span class="text-lg font-bold font-black text-success">{{ number_format($currency->exchange_rate, 2) }}</span>
                                            <span class="text-lg font-bold text-gray-400">BDT</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    {{-- Theme toggle buttons --}}
                    <a href="javascript:;" x-cloak x-show="$store.app.theme === 'light'" href="javascript:;"
                        class="flex items-center rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60"
                        @click="$store.app.toggleTheme('dark')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.5" />
                            <path d="M12 2V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M12 20V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M4 12L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M22 12L20 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path opacity="0.5" d="M19.7778 4.22266L17.5558 6.25424" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" />
                            <path opacity="0.5" d="M4.22217 4.22266L6.44418 6.25424" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" />
                            <path opacity="0.5" d="M6.44434 17.5557L4.22211 19.7779" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" />
                            <path opacity="0.5" d="M19.7778 19.7773L17.5558 17.5551" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </a>
                    <a href="javascript:;" x-cloak x-show="$store.app.theme === 'dark'" href="javascript:;"
                        class="flex items-center rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60"
                        @click="$store.app.toggleTheme('system')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0672 11.8568L20.4253 11.469L21.0672 11.8568ZM12.1432 2.93276L11.7553 2.29085V2.29085L12.1432 2.93276ZM21.25 12C21.25 17.1086 17.1086 21.25 12 21.25V22.75C17.9371 22.75 22.75 17.9371 22.75 12H21.25ZM12 21.25C6.89137 21.25 2.75 17.1086 2.75 12H1.25C1.25 17.9371 6.06294 22.75 12 22.75V21.25ZM2.75 12C2.75 6.89137 6.89137 2.75 12 2.75V1.25C6.06294 1.25 1.25 6.06294 1.25 12H2.75ZM15.5 14.25C12.3244 14.25 9.75 11.6756 9.75 8.5H8.25C8.25 12.5041 11.4959 15.75 15.5 15.75V14.25ZM20.4253 11.469C19.4172 13.1373 17.5882 14.25 15.5 14.25V15.75C18.1349 15.75 20.4407 14.3439 21.7092 12.2447L20.4253 11.469ZM9.75 8.5C9.75 6.41182 10.8627 4.5828 12.531 3.57467L11.7553 2.29085C9.65609 3.5593 8.25 5.86509 8.25 8.5H9.75ZM12 2.75C11.9115 2.75 11.8077 2.71008 11.7324 2.63168C11.6686 2.56527 11.6538 2.50244 11.6503 2.47703C11.6461 2.44587 11.6482 2.35557 11.7553 2.29085L12.531 3.57467C13.0342 3.27065 13.196 2.71398 13.1368 2.27627C13.0754 1.82126 12.7166 1.25 12 1.25V2.75ZM21.7092 12.2447C21.6444 12.3518 21.5541 12.3539 21.523 12.3497C21.4976 12.3462 21.4347 12.3314 21.3683 12.2676C21.2899 12.1923 21.25 12.0885 21.25 12H22.75C22.75 11.2834 22.1787 10.9246 21.7237 10.8632C21.286 10.804 20.7293 10.9658 20.4253 11.469L21.7092 12.2447Z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="javascript:;" x-cloak x-show="$store.app.theme === 'system'" href="javascript:;"
                        class="flex items-center rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60"
                        @click="$store.app.toggleTheme('light')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3 9C3 6.17157 3 4.75736 3.87868 3.87868C4.75736 3 6.17157 3 9 3H15C17.8284 3 19.2426 3 20.1213 3.87868C21 4.75736 21 6.17157 21 9V14C21 15.8856 21 16.8284 20.4142 17.4142C19.8284 18 18.8856 18 17 18H7C5.11438 18 4.17157 18 3.58579 17.4142C3 16.8284 3 15.8856 3 14V9Z"
                                stroke="currentColor" stroke-width="1.5" />
                            <path opacity="0.5" d="M22 21H2" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" />
                            <path opacity="0.5" d="M15 15H9" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </a>
                </div>

                <!-- Staff Live Attendance Header Status / Quick Action Dropdown -->
                @if(auth()->check())
                    @php
                        $headerAtt = auth()->user()->todayAttendance;
                        $headerSettings = \App\Models\AttendanceSetting::getSettings();
                    @endphp
                    <div class="dropdown flex shrink-0" x-data="headerAttendance()" @click.outside="open = false">
                        <!-- Header Trigger Pill -->
                        @if(!$headerAtt)
                            <button type="button" 
                                    @click="toggle"
                                    class="group inline-flex items-center rounded-full border border-primary/30 bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary shadow-sm transition-all duration-200 hover:bg-primary hover:text-white hover:shadow-md"
                                    style="gap: 4px;"
                                    title="Attendance not submitted today. Click to Check In.">
                                <span class="relative flex h-2 w-2">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-primary group-hover:bg-white"></span>
                                </span>
                                <span>Check In</span>
                            </button>
                        @elseif($headerAtt && !$headerAtt->check_out)
                            <button type="button" 
                                    @click="toggle"
                                    class="group inline-flex items-center rounded-full border border-success/30 bg-success/10 px-2.5 py-1 text-xs font-semibold text-success shadow-sm transition-all duration-200 hover:bg-success hover:text-white hover:shadow-md"
                                    style="gap: 4px;"
                                    title="Checked In at {{ optional($headerAtt->check_in)->format('h:i A') ?? '--:--' }}. Click to Check Out.">
                                <span class="relative flex h-2 w-2">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-success group-hover:bg-white"></span>
                                </span>
                                <span>In: <strong class="font-bold">{{ optional($headerAtt->check_in)->format('h:i A') ?? '--:--' }}</strong></span>
                            </button>
                        @else
                            <button type="button" 
                                    @click="toggle"
                                    class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 transition-all hover:bg-slate-200 dark:border-dark dark:bg-dark/60 dark:text-slate-300"
                                    style="gap: 4px;"
                                    title="Shift Completed today ({{ $headerAtt->total_hours }} hrs)">
                                <span class="h-2 w-2 rounded-full bg-success"></span>
                                <span>Done ({{ $headerAtt->total_hours }}h)</span>
                            </button>
                        @endif

                        <!-- Seamless Quick Attendance Dropdown -->
                        <ul x-cloak x-show="open" x-transition x-transition.duration.300ms
                            class="top-11 w-[320px] !py-0 font-semibold text-dark ltr:right-0 rtl:left-0 dark:text-white-dark dark:text-white-light/90 shadow-2xl rounded-xl border border-[#e0e6ed] bg-white dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                            <!-- Dropdown Header -->
                            <li class="border-b border-[#e0e6ed] bg-slate-50 px-4 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-sm font-bold text-dark dark:text-white-light">
                                        Work Attendance
                                    </h5>
                                    @if(!$headerAtt)
                                        <span class="badge bg-danger/10 text-danger border border-danger/20 text-[10px] font-bold px-2 py-0.5 rounded">Not Checked In</span>
                                    @elseif(!$headerAtt->check_out)
                                        <span class="badge bg-success/10 text-success border border-success/20 text-[10px] font-bold px-2 py-0.5 rounded">Checked In</span>
                                    @else
                                        <span class="badge bg-info/10 text-info border border-info/20 text-[10px] font-bold px-2 py-0.5 rounded">Shift Done</span>
                                    @endif
                                </div>
                            </li>

                            <!-- Dropdown Body -->
                            <li class="p-4 space-y-3 font-normal text-xs" @click.stop>
                                <!-- Shift details -->
                                <div class="space-y-1.5 text-white-dark text-[11px] bg-slate-50 dark:bg-dark/40 p-3 rounded-lg border border-slate-100 dark:border-dark">
                                    <div class="flex justify-between">
                                        <span>Shift Hours:</span>
                                        <span class="font-semibold text-dark dark:text-white-light">{{ \Carbon\Carbon::parse($headerSettings->shift_start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($headerSettings->shift_end_time)->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Grace Period:</span>
                                        <span class="font-semibold text-dark dark:text-white-light">+{{ $headerSettings->late_grace_minutes }} mins</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Office:</span>
                                        <span class="font-semibold text-dark dark:text-white-light truncate max-w-[150px]">{{ $headerSettings->office_name }}</span>
                                    </div>
                                    @if($headerAtt)
                                        <div class="flex justify-between pt-1.5 border-t border-slate-200 dark:border-dark text-dark dark:text-white-light">
                                            <span>Check-In Time:</span>
                                            <strong class="font-bold text-success">{{ optional($headerAtt->check_in)->format('h:i A') ?? '--:--' }}</strong>
                                        </div>
                                        @if($headerAtt->check_out)
                                            <div class="flex justify-between text-dark dark:text-white-light">
                                                <span>Check-Out Time:</span>
                                                <strong class="font-bold text-primary">{{ optional($headerAtt->check_out)->format('h:i A') ?? '--:--' }}</strong>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                <!-- Action Button Inside Dropdown -->
                                @if(!$headerAtt)
                                    <button type="button" 
                                            @click="submitCheckIn()" 
                                            :disabled="loading" 
                                            class="btn btn-success w-full gap-2 py-2 text-xs font-bold shadow-sm hover:shadow transition flex items-center justify-center">
                                        <svg x-show="!loading" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                                        <span x-show="loading" class="animate-spin inline-block h-3.5 w-3.5 border-2 border-white border-l-transparent rounded-full"></span>
                                        <span x-text="loading ? 'Verifying GPS...' : 'Check In Now'"></span>
                                    </button>
                                @elseif(!$headerAtt->check_out)
                                    <button type="button" 
                                            @click="submitCheckOut()" 
                                            :disabled="loading" 
                                            class="btn btn-primary w-full gap-2 py-2 text-xs font-bold shadow-sm hover:shadow transition flex items-center justify-center">
                                        <svg x-show="!loading" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        <span x-show="loading" class="animate-spin inline-block h-3.5 w-3.5 border-2 border-white border-l-transparent rounded-full"></span>
                                        <span x-text="loading ? 'Recording...' : 'Check Out Now'"></span>
                                    </button>
                                @else
                                    <div class="p-2.5 rounded-lg bg-success/10 text-success text-center text-xs font-bold flex items-center justify-center gap-1.5 border border-success/20">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        Shift Completed ({{ $headerAtt->total_hours }} hrs)
                                    </div>
                                @endif

                                <!-- Error feedback -->
                                <div x-cloak x-show="errorMessage" class="text-[11px] text-danger font-semibold flex items-start gap-1 p-2 rounded bg-danger/10 border border-danger/20">
                                    <svg class="h-3.5 w-3.5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <span x-text="errorMessage"></span>
                                </div>
                            </li>

                            <!-- Dropdown Footer -->
                            <li class="border-t border-[#e0e6ed] bg-slate-50 px-4 py-2.5 text-center dark:border-[#1b2e4b] dark:bg-[#121e32]">
                                <a href="{{ route('admin.attendance.my-attendance') }}" 
                                   class="text-xs font-bold text-primary hover:underline flex items-center justify-center gap-1">
                                    <span>View My Attendance History</span>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <script>
                    function headerAttendance() {
                        return {
                            open: false,
                            loading: false,
                            errorMessage: '',
                            toggle() {
                                this.open = !this.open;
                                this.errorMessage = '';
                            },
                            submitCheckIn() {
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
                                        this.errorMessage = 'Network error: ' + err.message;
                                    });
                                };

                                if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition(
                                        pos => sendPayload(pos.coords.latitude, pos.coords.longitude),
                                        () => sendPayload(null, null),
                                        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                                    );
                                } else {
                                    sendPayload(null, null);
                                }
                            },
                            submitCheckOut() {
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
                                        this.errorMessage = 'Network error: ' + err.message;
                                    });
                                };

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

                <div class="dropdown flex shrink-0" x-data="dropdown" @click.outside="open = false">
                    <a href="javascript:;"
                        class="relative block rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60"
                        @click="toggle">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.0001 2C8.13407 2 5.00006 5.13401 5.00006 9V9.7041C5.00006 10.5491 4.74995 11.3752 4.28123 12.0783L3.13263 13.8012C2.08349 15.3749 2.88442 17.5139 4.70913 18.0116C9.48258 19.3134 14.5175 19.3134 19.291 18.0116C21.1157 17.5139 21.9166 15.3749 20.8675 13.8012L19.7189 12.0783C19.2502 11.3752 19.0001 10.5491 19.0001 9.7041V9C19.0001 5.13401 15.8661 2 12.0001 2Z"
                                stroke="currentColor" stroke-width="1.5" />
                            <path opacity="0.5"
                                d="M7.5 19C8.15503 20.7478 9.92246 22 12 22C14.0775 22 15.845 20.7478 16.5 19"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        @if (auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 flex h-3 w-3 ltr:right-0 rtl:left-0">
                                <span
                                    class="absolute -top-[3px] inline-flex h-full w-full animate-ping rounded-full bg-danger/50 opacity-75 ltr:-right-[3px] rtl:-left-[3px]"></span>
                                <span
                                    class="relative inline-flex h-[6px] w-[6px] rounded-full bg-danger ltr:right-0 rtl:left-0"></span>
                            </span>
                        @endif
                    </a>
                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms
                        class="top-11 w-[300px] !py-0 font-semibold text-dark ltr:right-0 rtl:left-0 dark:text-white-dark dark:text-white-light/90">
                        <li class="mb-5" @click.stop>
                            <div
                                class="flex items-center justify-between !overflow-hidden rounded-t-md bg-white-light/40 px-4 py-2 dark:bg-dark/40">
                                <h4 class="text-lg font-bold">Notifications</h4>
                                @if (auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                    <span
                                        class="badge badge-primary">{{ auth()->user()->unreadNotifications->count() }}
                                        New</span>
                                @endif
                            </div>
                        </li>
                        @if(auth()->check())
                            @php $notifications = auth()->user()->unreadNotifications; @endphp
                            @forelse($notifications as $notification)
                                <li>
                                    <a href="{{ route('admin.notifications.read', $notification->id) }}"
                                        class="!grid grid-cols-[auto_1fr] gap-2 !px-4 !py-3 hover:bg-gray-100 dark:hover:bg-[#191e3a]"
                                        @click="toggle">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-success/10 text-success">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"
                                                    fill="currentColor" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h6 class="text-sm font-bold">
                                                {{ $notification->data['message'] ?? 'New Notification' }}</h6>
                                            <p class="text-xs text-white-dark/50">
                                                {{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li>
                                    <div class="!px-4 !py-3 text-center hover:bg-gray-100 dark:hover:bg-[#191e3a]">
                                        <p class="text-sm font-semibold text-white-dark/50">No new notifications</p>
                                    </div>
                                </li>
                            @endforelse
                        @endif
                        <li class="border-t border-white-light/40 dark:border-dark/40">
                            <a href="{{ route('admin.notifications.readAll') }}"
                                class="!py-3 text-center hover:!bg-transparent text-primary hover:underline">
                                Mark all as read
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="dropdown flex shrink-0" x-data="dropdown" @click.outside="open = false">
                    <a href="javascript:;" class="group relative" @click="toggle">
                        <span>
                            <img class="h-9 w-9 rounded-full object-cover saturate-50 group-hover:saturate-100"
                                src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&rounded=true&background=random"
                                alt="userProfile" />
                        </span>
                    </a>
                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms
                        class="top-11 w-[230px] !py-0 font-semibold text-dark ltr:right-0 rtl:left-0 dark:text-white-dark dark:text-white-light/90">
                        <li>
                            <div class="flex items-center px-4 py-4">
                                <img class="h-10 w-10 rounded-md object-cover"
                                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&rounded=true&background=random"
                                    alt="userProfile" />
                                <div class="truncate ltr:pl-4 rtl:pr-4">
                                    <h4 class="text-base">
                                        {{ auth()->user()->name ?? 'Guest' }}
                                        <!-- <span
                                            class="rounded bg-success-light px-1 text-xs text-success ltr:ml-2 rtl:ml-2">Pro</span> -->
                                    </h4>
                                    <button type="button"
                                        class="text-black/60 hover:text-primary dark:text-dark-light/60 dark:hover:text-white">
                                        {{ \Illuminate\Support\Str::of(auth()->user()->email ?? '')->before('@')->limit(5, '......') }}@....{{ \Illuminate\Support\Str::of(auth()->user()->email ?? '')->afterLast('.') }}
                                    </button>
                                </div>
                            </div>
                        </li>
                        <!-- <li>
                            <a href="#" class="dark:hover:text-white" @click="toggle">
                                <svg class="shrink-0 ltr:mr-2 rtl:ml-2" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="6" r="4" stroke="currentColor" stroke-width="1.5" />
                                    <path opacity="0.5"
                                        d="M20 17.5C20 19.9853 20 22 12 22C4 22 4 19.9853 4 17.5C4 15.0147 7.58172 13 12 13C16.4183 13 20 15.0147 20 17.5Z"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                Profile
                            </a>
                        </li> -->
                        <li class="border-t border-white-light dark:border-white-light/10">
                            <form method="POST" action="{{ route('tyro-login.logout') }}" id="logout-form"
                                class="hidden">
                                @csrf
                            </form>
                            <a href="javascript:;" class="!py-3 text-danger"
                                onclick="document.getElementById('logout-form').submit();">
                                <svg class="shrink-0 rotate-90 ltr:mr-2 rtl:ml-2" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.5"
                                        d="M17 9.00195C19.175 9.01406 20.3529 9.11051 21.1213 9.8789C22 10.7576 22 12.1718 22 15.0002V16.0002C22 18.8286 22 20.2429 21.1213 21.1215C20.2426 22.0002 18.8284 22.0002 16 22.0002H8C5.17157 22.0002 3.75736 22.0002 2.87868 21.1215C2 20.2429 2 18.8286 2 16.0002L2 15.0002C2 12.1718 2 10.7576 2.87868 9.87889C3.64706 9.11051 4.82497 9.01406 7 9.00195"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Sign Out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
