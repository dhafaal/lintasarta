<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class AttendancesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithEvents
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    protected string $type;
    protected ?int $month;
    protected ?int $year;
    protected ?int $userId;

    public function __construct(string $type, ?int $month = null, ?int $year = null, ?int $userId = null)
    {
        $this->type = $type;
        $this->month = $month;
        $this->year = $year;
        $this->userId = $userId;
    }

    public function collection(): Collection
    {
        $schedulesQuery = \App\Models\Schedules::with(['user', 'shift']);

        if ($this->type === 'monthly' && $this->year && $this->month) {
            $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
        } elseif ($this->type === 'yearly' && $this->year) {
            $start = Carbon::create($this->year, 1, 1)->startOfDay();
            $end = $start->copy()->endOfYear();
            $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
        } elseif ($this->type === 'user') {
            if ($this->year && $this->month) {
                $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
                $end = $start->copy()->endOfMonth();
                $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
            } elseif ($this->year) {
                $start = Carbon::create($this->year, 1, 1)->startOfDay();
                $end = $start->copy()->endOfYear();
                $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
            }
        }

        if ($this->type === 'user' && $this->userId) {
            $schedulesQuery->where('user_id', $this->userId);
        }

        $schedules = $schedulesQuery->get();
        $scheduleIds = $schedules->pluck('id');
        $attendances = Attendance::whereIn('schedule_id', $scheduleIds)->get()->keyBy('schedule_id');

        $combinedData = $schedules->map(function ($schedule) use ($attendances) {
            $attendance = $attendances->get($schedule->id);
            return (object) [
                'schedule' => $schedule,
                'user' => $schedule->user,
                'check_in_time' => $attendance->check_in_time ?? null,
                'check_out_time' => $attendance->check_out_time ?? null,
                'status' => $attendance->status ?? 'alpha',
                'late_minutes' => $attendance->late_minutes ?? 0,
            ];
        });

        return $combinedData->sortBy(fn($item) => $item->schedule->schedule_date ?? '9999-12-31')->values();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Nama', 'Shift', 'Kategori Shift', 'Check In', 'Check Out', 'Status'];
    }

    public function map($item): array
    {
        $schedule = $item->schedule;
        $user = $item->user;
        $shift = $schedule->shift;

        return [
            $schedule->schedule_date ? Carbon::parse($schedule->schedule_date)->format('Y-m-d') : '',
            $user->name ?? '-',
            $shift->shift_name ?? '-',
            $shift->category ?? '-',
            $item->check_in_time ? Carbon::parse($item->check_in_time)->format('H:i:s') : '',
            $item->check_out_time ? Carbon::parse($item->check_out_time)->format('H:i:s') : '',
            strtolower($item->status ?? 'alpha'),
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
{
    $rowCount = $sheet->getHighestRow();

    // ===== HEADER STYLE =====
    $sheet->getStyle('A1:G1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 12,
            'color' => ['argb' => 'FF1E293B'], // Slate-800
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFF1F5F9'], // Slate-100
        ],
        'borders' => [
            'bottom' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FFE2E8F0'], // Light gray
            ],
        ],
    ]);

    // Set tinggi header agar lebih lapang
    $sheet->getRowDimension(1)->setRowHeight(28);

    // ===== BODY STYLE =====
    for ($row = 2; $row <= $rowCount; $row++) {
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font' => [
                'size' => 11,
                'color' => ['argb' => 'FF1E293B'], // Slate-800
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_HAIR,
                    'color' => ['argb' => 'FFE5E7EB'], // super light border
                ],
            ],
        ]);

        // Zebra stripes minimalis (hanya background tipis)
        if ($row % 2 === 0) {
            $sheet->getStyle("A{$row}:G{$row}")
                ->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFAFAFA'); // Soft gray
        }
    }

    // Lebarkan semua kolom biar lebih "lapang"
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    return [];
}


    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'E' => NumberFormat::FORMAT_DATE_TIME4,
            'F' => NumberFormat::FORMAT_DATE_TIME4,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = $sheet->getHighestRow();

                for ($row = 2; $row <= $rowCount; $row++) {
                    $status = strtolower($sheet->getCell("G{$row}")->getValue());
                    $fillColor = null;
                    $textColor = null;

                    switch ($status) {
                        case 'hadir':
                            $fillColor = 'FFBBF7D0'; // green-200
                            $textColor = 'FF166534'; // green-800
                            break;
                        case 'telat':
                            $fillColor = 'FFFED7AA'; // orange-200
                            $textColor = 'FF9A3412'; // orange-800
                            break;
                        case 'izin':
                            $fillColor = 'FFFEF08A'; // yellow-200
                            $textColor = 'FFA16207'; // amber-700
                            break;
                        case 'alpha':
                            $fillColor = 'FFFECACA'; // red-200
                            $textColor = 'FF991B1B'; // red-700
                            break;
                    }

                    if ($fillColor) {
                        $style = $sheet->getStyle("G{$row}");
                        $style->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($fillColor);
                        $style->getFont()->getColor()->setARGB($textColor);
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }
            },
        ];
    }
}
