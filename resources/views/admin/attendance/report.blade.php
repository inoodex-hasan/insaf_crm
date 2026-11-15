@extends('admin.layouts.master')

@section('title', 'Monthly Attendance & Salary Deduction Report')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase text-dark dark:text-white-light">Monthly Attendance & Salary Deductions</h2>
            <p class="text-xs text-white-dark mt-1">Review calculated attendance penalties per staff member based on Super Admin dynamic rules.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.salaries.generate', ['month' => $month]) }}" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Generate Monthly Salaries (With Auto-Deductions)
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary gap-2">
                Daily Board
            </a>
            <a href="{{ route('admin.attendance.settings') }}" class="btn btn-outline-info gap-2">
                Settings
            </a>
        </div>
    </div>

    <!-- Month Filter & Active Policy Notice -->
    <div class="panel">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <form action="{{ route('admin.attendance.report') }}" method="GET" class="flex items-center gap-3">
                <label class="text-xs font-semibold text-white-dark whitespace-nowrap">Select Month:</label>
                <input type="month" name="month" value="{{ $month }}" class="form-input text-xs w-48 font-bold" onchange="this.form.submit()" />
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>

            <div class="text-xs text-white-dark bg-gray-50 dark:bg-dark/40 px-3 py-2 rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b]">
                <strong class="text-dark dark:text-white-light">Active Deduction Policy:</strong> 
                @if($settings->late_deduction_mode === 'quota')
                    Every {{ $settings->late_quota_days }} late = {{ $settings->late_quota_penalty_days }} day pay |
                @elseif($settings->late_deduction_mode === 'fixed_amount')
                    BDT {{ number_format($settings->late_fixed_amount, 2) }} per late |
                @else
                    Late cuts {{ $settings->late_deduction_mode }} |
                @endif
                Absent: {{ $settings->absence_deduction_rate }}x day rate | Half-day: {{ $settings->half_day_deduction_rate }}x day rate
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="panel">
        <div class="datatable">
            <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th class="text-center">Working Days</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Late Days</th>
                            <th class="text-center">Half Days</th>
                            <th class="text-center">Absent Days</th>
                            <th class="text-right">Est. Daily Rate</th>
                            <th class="text-right">Late Penalty</th>
                            <th class="text-right">Absent Penalty</th>
                            <th class="text-right">Total Salary Deduction</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDeductionsSum = 0;
                        @endphp
                        @forelse($reportData as $row)
                            @php
                                $m = $row['metrics'];
                                $totalDeductionsSum += $m['total_attendance_deduction'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="font-bold text-dark dark:text-white-light">{{ $row['user']->name }}</div>
                                    <div class="text-xs text-white-dark">{{ $row['user']->email }}</div>
                                    <div class="text-[10px] text-primary uppercase font-bold mt-0.5">{{ $row['user']->designation ?? 'Staff' }}</div>
                                </td>

                                <td class="text-center font-semibold text-xs">{{ $m['working_days'] }}</td>
                                <td class="text-center text-xs text-success font-bold">{{ $m['present_days'] }}</td>
                                
                                <td class="text-center text-xs">
                                    <span class="{{ $m['late_days'] > 0 ? 'text-warning font-bold' : 'text-white-dark' }}">
                                        {{ $m['late_days'] }}
                                    </span>
                                </td>

                                <td class="text-center text-xs">
                                    <span class="{{ $m['half_days'] > 0 ? 'text-info font-bold' : 'text-white-dark' }}">
                                        {{ $m['half_days'] }}
                                    </span>
                                </td>

                                <td class="text-center text-xs">
                                    <span class="{{ $m['absent_days'] > 0 ? 'text-danger font-bold' : 'text-white-dark' }}">
                                        {{ $m['absent_days'] }}
                                    </span>
                                </td>

                                <td class="text-right font-mono text-xs">
                                    BDT {{ number_format($m['daily_rate'], 2) }}
                                </td>

                                <td class="text-right font-mono text-xs text-warning font-semibold">
                                    BDT {{ number_format($m['late_deduction'], 2) }}
                                </td>

                                <td class="text-right font-mono text-xs text-danger font-semibold">
                                    BDT {{ number_format($m['absence_deduction'] + $m['half_day_deduction'], 2) }}
                                </td>

                                <td class="text-right font-mono text-sm font-bold text-danger">
                                    BDT {{ number_format($m['total_attendance_deduction'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-8 text-white-dark">No staff members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-dark font-bold">
                            <td colspan="9" class="text-right py-3 text-xs uppercase">Total Monthly Attendance Deductions:</td>
                            <td class="text-right py-3 font-mono text-base text-danger">BDT {{ number_format($totalDeductionsSum, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
