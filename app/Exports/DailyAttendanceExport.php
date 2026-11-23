<?php

namespace App\Exports;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyAttendanceExport implements FromView, ShouldAutoSize, WithStyles
{
    protected ?string $date;
    protected ?string $status;
    protected ?int $userId;
    protected ?string $search;

    public function __construct(?string $date, ?string $status = null, ?int $userId = null, ?string $search = null)
    {
        $this->date = $date ?: now()->toDateString();
        $this->status = $status;
        $this->userId = $userId;
        $this->search = $search;
    }

    public function view(): View
    {
        $query = Attendance::with(['user', 'adjuster'])
            ->where('date', $this->date)
            ->orderBy('check_in', 'asc');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->search) {
            $search = $this->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $attendances = $query->get();
        $formattedDate = Carbon::parse($this->date)->format('d F Y (l)');

        return view('admin.exports.attendance_daily', [
            'attendances' => $attendances,
            'date' => $this->date,
            'formattedDate' => $formattedDate,
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
