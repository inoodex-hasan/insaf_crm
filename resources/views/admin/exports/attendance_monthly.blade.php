<table>
    <thead>
        <tr>
            <th colspan="12" style="font-size: 14pt; font-weight: bold; text-align: left; color: #1e293b;">
                INSAF IMMIGRATION CONSULTANCY
            </th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 11pt; font-weight: bold; text-align: left; color: #2563eb;">
                Monthly Staff Attendance &amp; Salary Deductions Report - {{ $formattedMonth }}
            </th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 9pt; color: #64748b; text-align: left;">
                Generated On: {{ now()->format('d M, Y h:i A') }} | Active Policy: 
                @if($settings->late_deduction_mode === 'quota')
                    Every {{ $settings->late_quota_days }} late = {{ $settings->late_quota_penalty_days }} day pay |
                @elseif($settings->late_deduction_mode === 'fixed_amount')
                    BDT {{ number_format($settings->late_fixed_amount, 2) }} per late |
                @else
                    Late: {{ $settings->late_deduction_mode }} |
                @endif
                Absent: {{ $settings->absence_deduction_rate }}x day rate | Half-day: {{ $settings->half_day_deduction_rate }}x day rate
            </th>
        </tr>
        <tr></tr>
        <tr style="background-color: #2563eb; color: #ffffff; font-weight: bold;">
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">#</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: left; border: 1px solid #cbd5e1;">Staff Name</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: left; border: 1px solid #cbd5e1;">Email</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: left; border: 1px solid #cbd5e1;">Designation</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Working Days</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Present</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Late Days</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Half Days</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Absent Days</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: right; border: 1px solid #cbd5e1;">Daily Rate (BDT)</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: right; border: 1px solid #cbd5e1;">Late Penalty (BDT)</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: right; border: 1px solid #cbd5e1;">Absent/Half-day Penalty (BDT)</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: right; border: 1px solid #cbd5e1;">Total Salary Deduction (BDT)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalWorking = 0;
            $totalPresent = 0;
            $totalLate = 0;
            $totalHalf = 0;
            $totalAbsent = 0;
            $totalLatePenalty = 0;
            $totalAbsencePenalty = 0;
            $grandTotalDeduction = 0;
        @endphp
        @forelse($reportData as $index => $row)
            @php
                $m = $row['metrics'];
                $totalWorking += $m['working_days'];
                $totalPresent += $m['present_days'];
                $totalLate += $m['late_days'];
                $totalHalf += $m['half_days'];
                $totalAbsent += $m['absent_days'];
                $totalLatePenalty += $m['late_deduction'];
                $absenceAndHalfDeduction = $m['absence_deduction'] + $m['half_day_deduction'];
                $totalAbsencePenalty += $absenceAndHalfDeduction;
                $grandTotalDeduction += $m['total_attendance_deduction'];
            @endphp
            <tr>
                <td style="text-align: center; border: 1px solid #e2e8f0;">{{ $index + 1 }}</td>
                <td style="font-weight: bold; border: 1px solid #e2e8f0;">{{ $row['user']->name }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $row['user']->email }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $row['user']->designation ?? 'Staff' }}</td>
                <td style="text-align: center; border: 1px solid #e2e8f0;">{{ $m['working_days'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #16a34a; border: 1px solid #e2e8f0;">{{ $m['present_days'] }}</td>
                <td style="text-align: center; font-weight: {{ $m['late_days'] > 0 ? 'bold' : 'normal' }}; color: {{ $m['late_days'] > 0 ? '#d97706' : '#64748b' }}; border: 1px solid #e2e8f0;">{{ $m['late_days'] }}</td>
                <td style="text-align: center; font-weight: {{ $m['half_days'] > 0 ? 'bold' : 'normal' }}; color: {{ $m['half_days'] > 0 ? '#0284c7' : '#64748b' }}; border: 1px solid #e2e8f0;">{{ $m['half_days'] }}</td>
                <td style="text-align: center; font-weight: {{ $m['absent_days'] > 0 ? 'bold' : 'normal' }}; color: {{ $m['absent_days'] > 0 ? '#dc2626' : '#64748b' }}; border: 1px solid #e2e8f0;">{{ $m['absent_days'] }}</td>
                <td style="text-align: right; border: 1px solid #e2e8f0;">{{ number_format($m['daily_rate'], 2) }}</td>
                <td style="text-align: right; color: #d97706; font-weight: bold; border: 1px solid #e2e8f0;">{{ number_format($m['late_deduction'], 2) }}</td>
                <td style="text-align: right; color: #dc2626; font-weight: bold; border: 1px solid #e2e8f0;">{{ number_format($absenceAndHalfDeduction, 2) }}</td>
                <td style="text-align: right; color: #dc2626; font-weight: bold; border: 1px solid #e2e8f0;">{{ number_format($m['total_attendance_deduction'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="13" style="text-align: center; border: 1px solid #e2e8f0; padding: 20px;">No staff records found for this period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="4" style="text-align: right; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9;">TOTALS:</td>
            <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9;">{{ $totalWorking }}</td>
            <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9; color: #16a34a;">{{ $totalPresent }}</td>
            <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9; color: #d97706;">{{ $totalLate }}</td>
            <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9; color: #0284c7;">{{ $totalHalf }}</td>
            <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9; color: #dc2626;">{{ $totalAbsent }}</td>
            <td style="border: 1px solid #cbd5e1; background-color: #f1f5f9;">-</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9; color: #d97706;">{{ number_format($totalLatePenalty, 2) }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9; color: #dc2626;">{{ number_format($totalAbsencePenalty, 2) }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #cbd5e1; background-color: #f1f5f9; color: #dc2626; font-size: 11pt;">{{ number_format($grandTotalDeduction, 2) }}</td>
        </tr>
    </tfoot>
</table>
