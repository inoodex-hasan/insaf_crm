@extends('admin.layouts.master')

@section('title', 'My Attendance History')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase text-dark dark:text-white-light">My Attendance History</h2>
            <p class="text-xs text-white-dark mt-1">Review your monthly check-in timestamps, working hours, and shift performance.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tyro-dashboard.index') }}" class="btn btn-outline-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard
            </a>
        </div>
    </div>

    <!-- Live Attendance Check-In / Check-Out Widget -->
    @include('admin.attendance.partials.dashboard-widget')

    <!-- Monthly Summary KPIs -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
        <div class="panel p-4 text-center">
            <div class="text-xs text-white-dark font-semibold uppercase">Working Days</div>
            <div class="text-xl font-bold text-dark dark:text-white-light mt-1">{{ $deductions['working_days'] }}</div>
        </div>

        <div class="panel p-4 text-center">
            <div class="text-xs text-white-dark font-semibold uppercase">Days Present</div>
            <div class="text-xl font-bold text-success mt-1">{{ $deductions['present_days'] }}</div>
        </div>

        <div class="panel p-4 text-center">
            <div class="text-xs text-white-dark font-semibold uppercase">Late Days</div>
            <div class="text-xl font-bold text-warning mt-1">{{ $deductions['late_days'] }}</div>
        </div>

        <div class="panel p-4 text-center">
            <div class="text-xs text-white-dark font-semibold uppercase">Half Days</div>
            <div class="text-xl font-bold text-info mt-1">{{ $deductions['half_days'] }}</div>
        </div>

        <div class="panel p-4 text-center">
            <div class="text-xs text-white-dark font-semibold uppercase">Absences</div>
            <div class="text-xl font-bold text-danger mt-1">{{ $deductions['absent_days'] }}</div>
        </div>

        <div class="panel p-4 text-center border-l-2 border-danger">
            <div class="text-xs text-white-dark font-semibold uppercase">Est. Deduction</div>
            <div class="text-lg font-bold font-mono text-danger mt-1">BDT {{ number_format($deductions['total_attendance_deduction'], 2) }}</div>
        </div>
    </div>

    <!-- Filter by Month -->
    <div class="panel">
        <form action="{{ route('admin.attendance.my-attendance') }}" method="GET" class="flex items-center gap-3">
            <label class="text-xs font-semibold text-white-dark whitespace-nowrap">Select Month:</label>
            <input type="month" name="month" value="{{ $month }}" class="form-input text-xs w-48 font-bold" onchange="this.form.submit()" />
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="panel">
        <div class="datatable">
            <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Status</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Total Hours</th>
                            <th>Distance</th>
                            <th>Notes / Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                            <tr>
                                <td class="font-bold text-dark dark:text-white-light text-xs">
                                    {{ $att->date->format('M d, Y') }}
                                </td>

                                <td class="text-xs text-white-dark">
                                    {{ $att->date->format('l') }}
                                </td>

                                <td>
                                    <span class="badge bg-{{ $att->status_color }}/10 text-{{ $att->status_color }} border border-{{ $att->status_color }}/20 text-xs font-semibold">
                                        {{ $att->status_label }}
                                    </span>
                                    @if($att->is_late && $att->late_minutes > 0)
                                        <div class="text-[10px] text-danger font-semibold mt-0.5">+{{ $att->late_minutes }}m late</div>
                                    @endif
                                </td>

                                <td class="text-xs font-semibold">
                                    {{ $att->check_in ? $att->check_in->format('h:i A') : '-' }}
                                </td>

                                <td class="text-xs font-semibold">
                                    {{ $att->check_out ? $att->check_out->format('h:i A') : ($att->check_in ? 'In Progress' : '-') }}
                                </td>

                                <td class="font-mono text-xs font-bold text-primary">
                                    {{ $att->total_hours > 0 ? $att->total_hours . ' hrs' : '-' }}
                                </td>

                                <td class="text-xs font-mono text-white-dark">
                                    {{ $att->check_in_distance_meters !== null ? $att->check_in_distance_meters . 'm' : '-' }}
                                </td>

                                <td class="text-xs text-white-dark">
                                    @if($att->is_manually_adjusted)
                                        <span class="text-info font-medium" title="{{ $att->adjustment_reason }}">
                                            Adjusted: {{ Str::limit($att->adjustment_reason, 30) }}
                                        </span>
                                    @else
                                        {{ $att->notes ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-white-dark">No attendance records found for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
