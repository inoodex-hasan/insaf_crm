@extends('admin.layouts.master')

@section('title', 'Attendance & Salary Deduction Settings')

@section('content')
<div class="space-y-6" x-data="attendanceSettingsHandler()">
    
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase text-dark dark:text-white-light">Attendance & Salary Settings</h2>
            <p class="text-xs text-white-dark mt-1">Configure office geofencing, IP whitelist, shift hours, and dynamic salary cut rules.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Daily Attendance Board
            </a>
            <a href="{{ route('admin.attendance.report') }}" class="btn btn-outline-info gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10M18 20V4M6 20v-4"/></svg>
                Monthly Deduction Report
            </a>
        </div>
    </div>

    <form action="{{ route('admin.attendance.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Section 1: Office Location & Geofencing -->
        <div class="panel">
            <div class="flex items-center justify-between border-b border-[#e0e6ed] pb-4 mb-5 dark:border-[#1b2e4b]">
                <div>
                    <h5 class="text-base font-bold text-dark dark:text-white-light">1. Office Location & Geofence Boundary</h5>
                    <p class="text-xs text-white-dark">Staff must be physically located within this radius to submit attendance.</p>
                </div>
                <button type="button" 
                        @click="detectLocation()" 
                        :disabled="detectingLocation"
                        class="btn btn-sm btn-outline-primary gap-1.5">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    <span x-text="detectingLocation ? 'Locating...' : 'Detect My Location'"></span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1">Office Name</label>
                    <input type="text" name="office_name" value="{{ old('office_name', $settings->office_name) }}" class="form-input" required />
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Office Latitude (GPS)</label>
                    <input type="text" name="office_latitude" x-model="latitude" class="form-input font-mono text-sm" required />
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Office Longitude (GPS)</label>
                    <input type="text" name="office_longitude" x-model="longitude" class="form-input font-mono text-sm" required />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                <div>
                    <label class="block text-xs font-semibold mb-1">
                        Allowed Geofence Radius (Meters)
                        <span class="text-primary font-bold ml-1" x-text="radius + ' m'"></span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" min="30" max="1000" step="10" x-model="radius" name="geofence_radius_meters" class="w-full accent-primary" />
                        <input type="number" min="30" max="5000" x-model="radius" class="form-input w-24 text-center font-bold" />
                    </div>
                    <span class="text-[11px] text-white-dark mt-1 block">Recommended: 100m – 200m to allow for indoor smartphone GPS drift.</span>
                </div>

                <div class="flex flex-col justify-center gap-3 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="enforce_geofence" value="1" {{ old('enforce_geofence', $settings->enforce_geofence) ? 'checked' : '' }} class="form-checkbox text-primary" />
                        <span class="text-sm font-semibold text-dark dark:text-white-light">Enforce Geofence Radius</span>
                    </label>
                    <span class="text-[11px] text-white-dark -mt-1 pl-6">When checked, check-ins beyond this radius will be blocked automatically.</span>
                </div>
            </div>
        </div>

        <!-- Section 2: IP Address / Office Network Whitelist -->
        <div class="panel">
            <div class="border-b border-[#e0e6ed] pb-4 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-base font-bold text-dark dark:text-white-light">2. Office Network & IP Address Whitelist</h5>
                <p class="text-xs text-white-dark">Restrict attendance submissions strictly to the office Wi-Fi / Broadband connection.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1">Allowed Public Office IPs (One per line or comma separated)</label>
                    <textarea name="allowed_ips" rows="3" class="form-textarea font-mono text-xs" placeholder="e.g. 103.145.22.10&#10;127.0.0.1">{{ old('allowed_ips', $settings->allowed_ips) }}</textarea>
                    <span class="text-[11px] text-white-dark mt-1 block">Current Client IP: <strong>{{ request()->ip() }}</strong></span>
                </div>

                <div class="flex flex-col justify-center gap-2 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="enforce_ip" value="1" {{ old('enforce_ip', $settings->enforce_ip) ? 'checked' : '' }} class="form-checkbox text-primary" />
                        <span class="text-sm font-semibold text-dark dark:text-white-light">Enforce IP Address Restriction</span>
                    </label>
                    <span class="text-[11px] text-white-dark pl-6">When checked, employees cannot submit attendance unless connected to the office Wi-Fi / authorized IP.</span>
                </div>
            </div>
        </div>

        <!-- Section 3: Office Work Shift & Hours -->
        <div class="panel">
            <div class="border-b border-[#e0e6ed] pb-4 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-base font-bold text-dark dark:text-white-light">3. Shift Timings & Grace Period</h5>
                <p class="text-xs text-white-dark">Set expected check-in hours and grace threshold for late detection.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1">Shift Start Time</label>
                    <input type="time" name="shift_start_time" value="{{ old('shift_start_time', substr($settings->shift_start_time, 0, 5)) }}" class="form-input" required />
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Late Grace Period (Minutes)</label>
                    <input type="number" name="late_grace_minutes" value="{{ old('late_grace_minutes', $settings->late_grace_minutes) }}" min="0" max="120" class="form-input" required />
                    <span class="text-[11px] text-white-dark mt-0.5 block">Arrival before this is On Time.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Half-Day Cutoff Time</label>
                    <input type="time" name="half_day_cutoff_time" value="{{ old('half_day_cutoff_time', substr($settings->half_day_cutoff_time, 0, 5)) }}" class="form-input" required />
                    <span class="text-[11px] text-white-dark mt-0.5 block">Arrival after this is Half Day.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Shift End Time</label>
                    <input type="time" name="shift_end_time" value="{{ old('shift_end_time', substr($settings->shift_end_time, 0, 5)) }}" class="form-input" required />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-semibold mb-1">Min Hours for Full Day</label>
                    <input type="number" step="0.5" name="min_hours_full_day" value="{{ old('min_hours_full_day', $settings->min_hours_full_day) }}" class="form-input" required />
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Min Hours for Half Day</label>
                    <input type="number" step="0.5" name="min_hours_half_day" value="{{ old('min_hours_half_day', $settings->min_hours_half_day) }}" class="form-input" required />
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Weekly Weekend Days</label>
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach(['Friday', 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'] as $day)
                            <label class="inline-flex items-center text-xs">
                                <input type="checkbox" name="weekend_days[]" value="{{ $day }}" 
                                    {{ in_array($day, (array) ($settings->weekend_days ?? ['Friday', 'Saturday'])) ? 'checked' : '' }} 
                                    class="form-checkbox text-primary mr-1" />
                                {{ $day }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Dynamic Salary Deduction Rules -->
        <div class="panel border-t-4 border-warning">
            <div class="border-b border-[#e0e6ed] pb-4 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-base font-bold text-dark dark:text-white-light">4. Dynamic Salary Deduction & Penalty Rules</h5>
                <p class="text-xs text-white-dark">Super Admin controls how late arrivals and unexcused absences trigger salary cuts during monthly payroll generation.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1">Salary Basis for Daily Rate</label>
                    <select name="salary_basis" class="form-select">
                        <option value="basic_salary" {{ old('salary_basis', $settings->salary_basis) === 'basic_salary' ? 'selected' : '' }}>Basic Salary Only</option>
                        <option value="gross_salary" {{ old('salary_basis', $settings->salary_basis) === 'gross_salary' ? 'selected' : '' }}>Gross Salary (Basic + Allowances)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Working Days Divisor</label>
                    <select name="salary_day_divisor_mode" class="form-select">
                        <option value="actual_working_days" {{ old('salary_day_divisor_mode', $settings->salary_day_divisor_mode) === 'actual_working_days' ? 'selected' : '' }}>Actual Working Days in Month (excluding weekends)</option>
                        <option value="fixed_30_days" {{ old('salary_day_divisor_mode', $settings->salary_day_divisor_mode) === 'fixed_30_days' ? 'selected' : '' }}>Fixed 30 Days</option>
                        <option value="calendar_days" {{ old('salary_day_divisor_mode', $settings->salary_day_divisor_mode) === 'calendar_days' ? 'selected' : '' }}>Total Days in Month (28–31)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Free/Grace Late Days per Month</label>
                    <input type="number" min="0" max="30" name="grace_late_days_per_month" value="{{ old('grace_late_days_per_month', $settings->grace_late_days_per_month) }}" class="form-input" />
                    <span class="text-[11px] text-white-dark mt-0.5 block">Number of late arrivals forgiven each month before penalty.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5 p-4 rounded-xl bg-gray-50 dark:bg-dark/40 border border-[#e0e6ed] dark:border-[#1b2e4b]">
                <!-- Late Policy -->
                <div>
                    <label class="block text-xs font-bold uppercase text-primary mb-1">Late Arrival Deduction Mode</label>
                    <select name="late_deduction_mode" x-model="lateMode" class="form-select text-sm font-semibold">
                        <option value="quota">Quota Rule (Every X late = Y days salary cut)</option>
                        <option value="fixed_amount">Fixed Amount per late day</option>
                        <option value="percentage">Percentage (25% day wage per late)</option>
                        <option value="disabled">No deduction for late arrivals</option>
                    </select>

                    <div x-show="lateMode === 'quota'" class="mt-3 space-y-2">
                        <div class="flex items-center gap-2 text-xs">
                            <span>Every</span>
                            <input type="number" min="1" max="30" name="late_quota_days" value="{{ old('late_quota_days', $settings->late_quota_days) }}" class="form-input w-16 text-center font-bold" />
                            <span>late arrivals =</span>
                            <input type="number" step="0.5" min="0.1" max="10" name="late_quota_penalty_days" value="{{ old('late_quota_penalty_days', $settings->late_quota_penalty_days) }}" class="form-input w-20 text-center font-bold" />
                            <span>day(s) salary cut</span>
                        </div>
                    </div>

                    <div x-show="lateMode === 'fixed_amount'" class="mt-3">
                        <label class="block text-xs font-semibold mb-1">BDT per Late Day</label>
                        <input type="number" step="10" min="0" name="late_fixed_amount" value="{{ old('late_fixed_amount', $settings->late_fixed_amount) }}" class="form-input" />
                    </div>
                </div>

                <!-- Absence Policy -->
                <div>
                    <label class="block text-xs font-bold uppercase text-danger mb-1">Unexcused Absence Penalty</label>
                    <div class="flex items-center gap-2 text-xs mt-2">
                        <span>Deduct</span>
                        <input type="number" step="0.1" min="0" max="5" name="absence_deduction_rate" value="{{ old('absence_deduction_rate', $settings->absence_deduction_rate) }}" class="form-input w-20 text-center font-bold" />
                        <span>day(s) salary per unexcused absent day</span>
                    </div>
                    <span class="text-[11px] text-white-dark mt-1 block">Default: 1.0 (1 full day wage cut per absent day).</span>
                </div>

                <!-- Half-Day Policy -->
                <div>
                    <label class="block text-xs font-bold uppercase text-info mb-1">Half-Day Penalty</label>
                    <div class="flex items-center gap-2 text-xs mt-2">
                        <span>Deduct</span>
                        <input type="number" step="0.1" min="0" max="2" name="half_day_deduction_rate" value="{{ old('half_day_deduction_rate', $settings->half_day_deduction_rate) }}" class="form-input w-20 text-center font-bold" />
                        <span>day(s) salary per half-day</span>
                    </div>
                    <span class="text-[11px] text-white-dark mt-1 block">Default: 0.5 (half-day wage cut).</span>
                </div>
            </div>

            <div class="mt-4 pt-3 flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="auto_apply_salary_deduction" value="1" {{ old('auto_apply_salary_deduction', $settings->auto_apply_salary_deduction) ? 'checked' : '' }} class="form-checkbox text-primary" />
                    <span class="text-sm font-semibold text-dark dark:text-white-light">Auto-Inject Attendance Deductions into Monthly Salary Generation</span>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="btn btn-primary gap-2 px-8 py-3 font-bold shadow-lg">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Attendance & Salary Settings
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('attendanceSettingsHandler', () => ({
        latitude: @js($settings->office_latitude),
        longitude: @js($settings->office_longitude),
        radius: @js($settings->geofence_radius_meters),
        lateMode: @js($settings->late_deduction_mode),
        detectingLocation: false,

        detectLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }
            this.detectingLocation = true;
            navigator.geolocation.getCurrentPosition(
                pos => {
                    this.latitude = pos.coords.latitude.toFixed(8);
                    this.longitude = pos.coords.longitude.toFixed(8);
                    this.detectingLocation = false;
                },
                err => {
                    alert('Could not acquire location: ' + err.message);
                    this.detectingLocation = false;
                },
                { enableHighAccuracy: true }
            );
        }
    }));
});
</script>
@endsection
