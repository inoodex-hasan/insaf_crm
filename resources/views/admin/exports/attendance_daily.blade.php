<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 14pt; font-weight: bold; text-align: left; color: #1e293b;">
                INSAF IMMIGRATION CONSULTANCY
            </th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 11pt; font-weight: bold; text-align: left; color: #2563eb;">
                Daily Staff Attendance Log - {{ $formattedDate }}
            </th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 9pt; color: #64748b; text-align: left;">
                Exported On: {{ now()->format('d M, Y h:i A') }} | Total Records: {{ $attendances->count() }}
            </th>
        </tr>
        <tr></tr>
        <tr style="background-color: #2563eb; color: #ffffff; font-weight: bold;">
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">#</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: left; border: 1px solid #cbd5e1;">Staff Name</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: left; border: 1px solid #cbd5e1;">Email</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Status</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Check-In</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Check-Out</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Total Hours</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1;">Geofence Distance</th>
            <th style="background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: left; border: 1px solid #cbd5e1;">IP Address &amp; Location</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attendances as $index => $row)
            @php
                $statusColor = match($row->status) {
                    'present' => '#16a34a',
                    'late' => '#d97706',
                    'half_day' => '#0284c7',
                    'absent' => '#dc2626',
                    default => '#475569'
                };
            @endphp
            <tr>
                <td style="text-align: center; border: 1px solid #e2e8f0;">{{ $index + 1 }}</td>
                <td style="font-weight: bold; border: 1px solid #e2e8f0;">{{ $row->user->name ?? 'N/A' }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $row->user->email ?? 'N/A' }}</td>
                <td style="text-align: center; font-weight: bold; color: {{ $statusColor }}; border: 1px solid #e2e8f0; text-transform: uppercase;">
                    {{ $row->status_label }}
                </td>
                <td style="text-align: center; border: 1px solid #e2e8f0;">
                    {{ $row->check_in ? $row->check_in->format('h:i A') : '-' }}
                </td>
                <td style="text-align: center; border: 1px solid #e2e8f0;">
                    {{ $row->check_out ? $row->check_out->format('h:i A') : '-' }}
                </td>
                <td style="text-align: center; font-weight: bold; border: 1px solid #e2e8f0;">
                    {{ $row->total_hours ? $row->total_hours . ' hrs' : '-' }}
                </td>
                <td style="text-align: center; border: 1px solid #e2e8f0;">
                    {{ $row->check_in_distance_meters !== null ? $row->check_in_distance_meters . 'm' : '-' }}
                </td>
                <td style="border: 1px solid #e2e8f0;">
                    {{ $row->check_in_ip ?? '-' }}
                    @if($row->office_name)
                        ({{ $row->office_name }})
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align: center; border: 1px solid #e2e8f0; padding: 20px;">No attendance records found for this date.</td>
            </tr>
        @endforelse
    </tbody>
</table>
