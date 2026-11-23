<?php

namespace App\Exports;

use App\Models\AttendanceSetting;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyAttendanceReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected string $month;
    protected AttendanceService $attendanceService;

    public function __construct(string $month)
    {
        $this->month = $month;
        $this->attendanceService = app(AttendanceService::class);
    }

    public function view(): View
    {
        $settings = AttendanceSetting::getSettings();
        $formattedMonth = Carbon::parse($this->month . '-01')->format('F Y');

        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin')->orWhere('name', 'superadmin');
        })->orderBy('name')->get();

        $reportData = [];
        $totalDeductions = 0;

        foreach ($users as $staff) {
            $metrics = $this->attendanceService->calculateMonthlyDeductions($staff, $this->month);
            $totalDeductions += $metrics['total_attendance_deduction'];
            $reportData[] = [
                'user' => $staff,
                'metrics' => $metrics,
            ];
        }

        return view('admin.exports.attendance_monthly', [
            'reportData' => $reportData,
            'month' => $this->month,
            'formattedMonth' => $formattedMonth,
            'settings' => $settings,
            'totalDeductions' => $totalDeductions,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E293B']]],
            2 => ['font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF64748B']]],
            5 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB']
                ]
            ],
        ];
    }
}
