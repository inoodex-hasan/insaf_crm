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

        <!-- Section 1: Office Locations & Geofencing (Multi-Office Support) -->
        <div class="panel">
            <div class="flex flex-wrap items-center justify-between border-b border-[#e0e6ed] pb-4 mb-5 dark:border-[#1b2e4b] gap-3">
                <div>
                    <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2">
                        <span>1. Office Locations & Geofence Boundaries</span>
                        <span class="badge bg-success/20 text-success text-[11px] font-bold px-2 py-0.5 rounded">Required</span>
                    </h5>
                    <p class="text-xs text-white-dark mt-0.5">Configure 1 or more office branches. Staff must be physically located within the radius of <strong>any</strong> registered office branch below to check in.</p>
                </div>

                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer bg-slate-50 dark:bg-dark/40 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-dark">
                        <input type="checkbox" name="enforce_geofence" value="1" {{ old('enforce_geofence', $settings->enforce_geofence) ? 'checked' : '' }} class="form-checkbox text-primary" />
                        <span class="text-xs font-bold text-dark dark:text-white-light">Enforce Geofence Radius</span>
                    </label>

                    <button type="button" @click="addOffice()" class="btn btn-sm btn-primary gap-1.5">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add Another Branch</span>
                    </button>
                </div>
            </div>

            <!-- List of Office Locations / Branches -->
            <div class="space-y-4">
                <template x-for="(office, index) in offices" :key="index">
                    <div class="rounded-xl border border-[#e0e6ed] bg-slate-50/50 p-4 dark:border-[#1b2e4b] dark:bg-[#121e32]/30 space-y-4">
                        <div class="flex items-center justify-between border-b border-dashed border-slate-200 dark:border-dark pb-2">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-bold" x-text="index + 1"></span>
                                <h6 class="text-sm font-bold text-dark dark:text-white-light" x-text="office.name || ('Branch #' + (index + 1))"></h6>
                                <template x-if="index === 0">
                                    <span class="badge bg-primary/10 text-primary text-[10px] font-semibold px-2 py-0.5 rounded">Primary / HQ</span>
                                </template>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        @click="detectOfficeLocation(index)" 
                                        :disabled="detectingMap[index]"
                                        class="btn btn-xs btn-outline-primary gap-1">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                                    <span x-text="detectingMap[index] ? 'Detecting...' : 'Detect GPS Here'"></span>
                                </button>

                                <template x-if="offices.length > 1">
                                    <button type="button" 
                                            @click="removeOffice(index)" 
                                            class="p-1 rounded text-danger hover:bg-danger/10 transition" 
                                            title="Remove this branch">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold mb-1">Branch / Office Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       :name="'office_locations[' + index + '][name]'" 
                                       x-model="office.name" 
                                       class="form-input text-xs" 
                                       placeholder="e.g. Dhaka Head Office or Sylhet Branch" 
                                       required />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold mb-1">Office Latitude (GPS) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       :name="'office_locations[' + index + '][latitude]'" 
                                       x-model="office.latitude" 
                                       class="form-input font-mono text-xs" 
                                       placeholder="e.g. 23.79250000" 
                                       required />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold mb-1">Office Longitude (GPS) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       :name="'office_locations[' + index + '][longitude]'" 
                                       x-model="office.longitude" 
                                       class="form-input font-mono text-xs" 
                                       placeholder="e.g. 90.40780000" 
                                       required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-semibold">Allowed Geofence Radius</label>
                                    <span class="text-primary font-bold text-xs" x-text="(office.radius_meters || 100) + ' meters'"></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="range" min="30" max="2000" step="10" 
                                           :name="'office_locations[' + index + '][radius_meters]'" 
                                           x-model="office.radius_meters" 
                                           class="w-full accent-primary" />
                                    <input type="number" min="30" max="5000" step="any" 
                                           x-model="office.radius_meters" 
                                           class="form-input w-24 text-center font-bold text-xs" />
                                </div>
                            </div>
                            <div class="flex items-center text-[11px] text-white-dark">
                                <span>Recommended: 100m – 200m to allow for indoor smartphone GPS drift inside the building.</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Add More Button at bottom of list -->
            <button type="button" 
                    @click="addOffice()" 
                    class="mt-4 w-full py-3 rounded-xl border-2 border-dashed border-slate-300 dark:border-dark text-slate-600 dark:text-slate-400 hover:border-primary hover:text-primary transition flex items-center justify-center gap-2 text-xs font-bold">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Add Another Office Branch / Location</span>
            </button>
        </div>

        <!-- Section 2: IP Address / Office Network Whitelist -->
        <div class="panel">
            <div class="border-b border-[#e0e6ed] pb-4 mb-5 dark:border-[#1b2e4b]">
                <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2">
                    <span>2. Office Network & IP Address Whitelist</span>
                    <span class="badge bg-warning/20 text-warning text-[11px] font-bold px-2 py-0.5 rounded">Optional</span>
                </h5>
                <p class="text-xs text-white-dark">Optional restriction. When disabled, staff can check in from any internet connection (mobile data or Wi-Fi) as long as they are at the office location.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1">Allowed Public Office IPs (Optional — One per line or comma separated)</label>
                    <textarea name="allowed_ips" rows="3" class="form-textarea font-mono text-xs" placeholder="Optional: Leave blank or uncheck enforcement if not restricting by IP...">{{ old('allowed_ips', $settings->allowed_ips) }}</textarea>
                    <span class="text-[11px] text-white-dark mt-1 block">Current Client IP: <strong>{{ request()->ip() }}</strong></span>
                </div>

                <div class="flex flex-col justify-center gap-2 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="enforce_ip" value="1" {{ old('enforce_ip', $settings->enforce_ip) ? 'checked' : '' }} class="form-checkbox text-primary" />
                        <span class="text-sm font-semibold text-dark dark:text-white-light">Enforce IP Address Restriction</span>
                    </label>
                    <span class="text-[11px] text-white-dark pl-6">Keep <strong>unchecked</strong> for IP to be optional. When checked, employees cannot submit attendance unless connected to the specified office IP.</span>
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
                    <input type="number" step="any" min="1" max="24" name="min_hours_full_day" value="{{ old('min_hours_full_day', $settings->min_hours_full_day) }}" class="form-input" required />
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1">Min Hours for Half Day</label>
                    <input type="number" step="any" min="1" max="12" name="min_hours_half_day" value="{{ old('min_hours_half_day', $settings->min_hours_half_day) }}" class="form-input" required />
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
                            <input type="number" step="any" min="0.1" max="10" name="late_quota_penalty_days" value="{{ old('late_quota_penalty_days', $settings->late_quota_penalty_days) }}" class="form-input w-20 text-center font-bold" />
                            <span>day(s) salary cut</span>
                        </div>
                    </div>

                    <div x-show="lateMode === 'fixed_amount'" class="mt-3">
                        <label class="block text-xs font-semibold mb-1">BDT per Late Day</label>
                        <input type="number" step="any" min="0" name="late_fixed_amount" value="{{ old('late_fixed_amount', $settings->late_fixed_amount) }}" class="form-input" />
                    </div>
                </div>

                <!-- Absence Policy -->
                <div>
                    <label class="block text-xs font-bold uppercase text-danger mb-1">Unexcused Absence Penalty</label>
                    <div class="flex items-center gap-2 text-xs mt-2">
                        <span>Deduct</span>
                        <input type="number" step="any" min="0" max="5" name="absence_deduction_rate" value="{{ old('absence_deduction_rate', $settings->absence_deduction_rate) }}" class="form-input w-20 text-center font-bold" />
                        <span>day(s) salary per unexcused absent day</span>
                    </div>
                    <span class="text-[11px] text-white-dark mt-1 block">Default: 1.0 (1 full day wage cut per absent day).</span>
                </div>

                <!-- Half-Day Policy -->
                <div>
                    <label class="block text-xs font-bold uppercase text-info mb-1">Half-Day Penalty</label>
                    <div class="flex items-center gap-2 text-xs mt-2">
                        <span>Deduct</span>
                        <input type="number" step="any" min="0" max="2" name="half_day_deduction_rate" value="{{ old('half_day_deduction_rate', $settings->half_day_deduction_rate) }}" class="form-input w-20 text-center font-bold" />
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
        offices: @json($settings->getAllOffices()),
        lateMode: @js(old('late_deduction_mode', $settings->late_deduction_mode)),
        detectingMap: {},

        addOffice() {
            this.offices.push({
                name: 'Office Branch ' + (this.offices.length + 1),
                latitude: '',
                longitude: '',
                radius_meters: 150
            });
        },

        removeOffice(index) {
            if (this.offices.length <= 1) {
                alert('You must have at least one office location configured.');
                return;
            }
            if (confirm('Are you sure you want to remove this office location?')) {
                this.offices.splice(index, 1);
            }
        },

        detectOfficeLocation(index) {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }
            this.detectingMap[index] = true;
            navigator.geolocation.getCurrentPosition(
                pos => {
                    this.offices[index].latitude = pos.coords.latitude.toFixed(8);
                    this.offices[index].longitude = pos.coords.longitude.toFixed(8);
                    this.detectingMap[index] = false;
                },
                err => {
                    alert('Could not acquire location: ' + err.message);
                    this.detectingMap[index] = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
    }));
});
</script>
@endsection
