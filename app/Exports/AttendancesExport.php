<?php

namespace App\Exports;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AttendancesExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithEvents, WithHeadings, WithMapping, WithStyles, WithDrawings
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    protected string $type;

    protected ?int $month;

    protected ?int $year;

    protected ?int $userId;

    public function __construct(string $type, ?int $month = null, ?int $year = null, ?int $userId = null)
    {
        $this->type   = $type;
        $this->month  = $month;
        $this->year   = $year;
        $this->userId = $userId;
    }

    public function collection(): Collection
    {
        $schedulesQuery = \App\Models\Schedules::with(['user', 'shift']);

        if ($this->type === 'monthly' && $this->year && $this->month) {
            $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
            $end   = $start->copy()->endOfMonth();
            $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
        } elseif ($this->type === 'yearly' && $this->year) {
            $start = Carbon::create($this->year, 1, 1)->startOfDay();
            $end   = $start->copy()->endOfYear();
            $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
        } elseif ($this->type === 'user') {
            if ($this->year && $this->month) {
                $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
                $end   = $start->copy()->endOfMonth();
                $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
            } elseif ($this->year) {
                $start = Carbon::create($this->year, 1, 1)->startOfDay();
                $end   = $start->copy()->endOfYear();
                $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
            }
        }

        if ($this->type === 'user' && $this->userId) {
            $schedulesQuery->where('user_id', $this->userId);
        }

        $schedules = $schedulesQuery->get();
        if ($schedules->isEmpty()) {
            return collect();
        }

        $scheduleIds    = $schedules->pluck('id');
        $attendancesRaw = Attendance::whereIn('schedule_id', $scheduleIds)
            ->with('schedule')
            ->get();
        $permissionsRaw = \App\Models\Permissions::whereIn('schedule_id', $scheduleIds)->get();

        // Group by user_id|schedule_date
        $groups = $schedules->groupBy(function ($s) {
            return ($s->user_id ?? '0').'|'.($s->schedule_date ?? '');
        });

        $rows = collect();
        foreach ($groups as $key => $daySchedules) {
            $firstSch = $daySchedules->first();
            $user     = $firstSch->user;
            $date     = $firstSch->schedule_date;

            // Shift names & categories
            $shiftNames = $daySchedules->map(fn ($s) => optional($s->shift)->shift_name)->filter()->values()->all();
            $categories = $daySchedules->map(fn ($s) => optional($s->shift)->category)->filter()->values()->all();

            // Attendances of the day for this user
            $dayAtts = $attendancesRaw->filter(function ($a) use ($daySchedules) {
                return $daySchedules->pluck('id')->contains($a->schedule_id);
            });

            // Times
            $firstCheckIn = $dayAtts->whereNotNull('check_in_time')->sortBy('check_in_time')->first();
            $lastCheckOut = $dayAtts->whereNotNull('check_out_time')->sortByDesc('check_out_time')->first();

            // Flags
            $hasCuti    = $dayAtts->where('status', 'cuti')->isNotEmpty();
            $hasForgot  = $dayAtts->where('status', 'forgot_checkout')->isNotEmpty();
            $hasEarly   = $dayAtts->where('status', 'early_checkout')->isNotEmpty();
            $hasIzin    = $dayAtts->where('status', 'izin')->isNotEmpty();
            $wasLate    = $dayAtts->filter(fn ($a) => $a && ($a->is_late || $a->status === 'telat'))->isNotEmpty();
            $wasPresent = $dayAtts->filter(fn ($a) => $a && $a->check_in_time)->isNotEmpty() || $dayAtts->whereIn('status', ['hadir', 'telat'])->isNotEmpty();

            // Work minutes (calendar rules) per day
            $dayWorkMinutesAcc = 0;
            foreach ($daySchedules as $schedule) {
                if (! $schedule->shift) {
                    continue;
                }
                $attendance = $dayAtts->first(function ($a) use ($schedule, $user) {
                    return $a->schedule_id == $schedule->id && $a->user_id == $user->id;
                });
                $permApproved = $permissionsRaw->first(function ($p) use ($schedule, $user) {
                    return $p->schedule_id == $schedule->id && $p->user_id == $user->id && $p->status === 'approved';
                });
                $permPending = $permissionsRaw->first(function ($p) use ($schedule, $user) {
                    return $p->schedule_id == $schedule->id && $p->user_id == $user->id && $p->status === 'pending';
                });

                // Shift duration (handle cross midnight)
                $startT = Carbon::parse($schedule->shift->start_time);
                $endT   = Carbon::parse($schedule->shift->end_time);
                if ($endT->lt($startT)) {
                    $endT->addDay();
                }
                $shiftMinutes = $startT->diffInMinutes($endT);

                // Rules align with calendar
                if ($attendance && $attendance->status === 'alpha') {
                    $m = 0;
                } elseif (! $attendance && ! $permApproved && ! $permPending) {
                    $m = 0;
                } else {
                    $m = $shiftMinutes;
                }
                $dayWorkMinutesAcc += $m;
            }
            $dayWorkMinutesAfterBreak = $dayWorkMinutesAcc > 0 ? max(0, $dayWorkMinutesAcc - 60) : 0;
            $workHoursDisplay         = (function ($m) {
                $h = $m / 60;

                return ($h == floor($h)) ? floor($h).'j' : number_format($h, 1).'j';
            })($dayWorkMinutesAfterBreak);

            // Status display
            if ($hasCuti) {
                $statusDisplay = 'Cuti';
            } elseif ($hasIzin) {
                $statusDisplay = 'Izin';
            } else {
                $parts = [];
                if ($wasPresent) {
                    $parts[] = $wasLate ? 'Telat' : 'Hadir';
                }
                if ($hasEarly) {
                    $parts[] = 'Early Checkout';
                }
                if ($hasForgot) {
                    $parts[] = 'Forgot Checkout';
                }
                if (empty($parts)) {
                    $parts[] = 'Alpha';
                }
                $statusDisplay = implode(', ', array_unique($parts));
            }

            $rows->push((object) [
                'date'               => $date,
                'user_name'          => $user->name ?? '-',
                'shift_names'        => implode(' ', $shiftNames),
                'categories'         => implode(' ', $categories),
                'check_in_time'      => optional($firstCheckIn)->check_in_time,
                'check_out_time'     => optional($lastCheckOut)->check_out_time,
                'work_minutes'       => $dayWorkMinutesAfterBreak,
                'work_hours_display' => $workHoursDisplay,
                'status_display'     => $statusDisplay,
            ]);
        }

        return $rows->sortBy(fn ($it) => $it->date ?? '9999-12-31')->values();
    }

    public function headings(): array
    {
        $typeLabel = '';
        if ($this->type === 'monthly') {
            $monthName = Carbon::createFromDate($this->year ?? date('Y'), $this->month ?? date('m'), 1)->translatedFormat('F Y');
            $typeLabel = "Periode: {$monthName}";
        } elseif ($this->type === 'yearly') {
            $typeLabel = "Periode Tahun: {$this->year}";
        } elseif ($this->type === 'user') {
            $userName = $this->userId ? (\App\Models\User::find($this->userId)->name ?? 'User') : 'Pegawai';
            if ($this->month && $this->year) {
                 $monthName = Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
                 $typeLabel = "Pegawai: {$userName} | Periode: {$monthName}";
            } else {
                 $typeLabel = "Pegawai: {$userName}";
            }
        } else {
            $typeLabel = "Laporan Kehadiran Karyawan";
        }

        return [
            ['PT. APLIKANUSA LINTASARTA'],
            ['LAPORAN KEHADIRAN PEGAWAI'],
            [$typeLabel],
            [],
            ['Tanggal', 'Nama', 'Shift', 'Kategori Shift', 'Check In', 'Check Out', 'Jam Kerja', 'Status']
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo Lintasarta');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('Logo-Lintasarta-new.webp'));
        $drawing->setHeight(65);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(10);

        return $drawing;
    }

    public function map($item): array
    {
        return [
            $item->date ? Carbon::parse($item->date)->format('Y-m-d') : '',
            $item->user_name ?? '-',
            $item->shift_names ?: '-',
            $item->categories ?: '-',
            $item->check_in_time ? Carbon::parse($item->check_in_time)->format('H:i:s') : '',
            $item->check_out_time ? Carbon::parse($item->check_out_time)->format('H:i:s') : '',
            $item->work_hours_display ?? '-',
            $item->status_display     ?? 'Alpha',
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $rowCount = $sheet->getHighestRow();

        // Style for titles
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => [
                'name'  => 'Arial',
                'color' => ['argb' => 'FF0A4B8F'], // Lintasarta Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(11)->getColor()->setARGB('FF4A5568');

        $sheet->getRowDimension(1)->setRowHeight(35);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(25);
        $sheet->getRowDimension(4)->setRowHeight(15);

        // ===== HEADER STYLE (Row 5) =====
        $sheet->getStyle('A5:H5')->applyFromArray([
            'font' => [
                'name'  => 'Arial',
                'bold'  => true,
                'size'  => 10,
                'color' => ['argb' => 'FFFFFFFF'], 
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0A4B8F'], // Lintasarta Dark Blue Header
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['argb' => 'FFFFFFFF'],
                ],
            ],
        ]);

        $sheet->getRowDimension(5)->setRowHeight(28);

        // ===== BODY STYLE =====
        for ($row = 6; $row <= $rowCount; $row++) {
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font' => [
                    'size'  => 9,
                    'name'  => 'Arial',
                    'color' => ['argb' => 'FF1A202C'], // Sangat gelap, almost black tapi lebih natural
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFCBD5E1'], // Border abu kebiruan tipis
                    ],
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFE2E8F0'],
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFE2E8F0'],
                    ]
                ],
            ]);

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF4F7FB'); // Latar biru sangat-sangat muda untuk zebra
            }
        }

        foreach (range('A', 'H') as $col) {
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
                $sheet    = $event->sheet->getDelegate();
                $rowCount = $sheet->getHighestRow();

                for ($row = 6; $row <= $rowCount; $row++) {
                    $statusCell = strtolower((string) $sheet->getCell("H{$row}")->getValue());
                    $fillColor  = null;
                    $textColor  = null;

                    // Detect keywords in combined status
                    if (strpos($statusCell, 'forgot checkout') !== false) {
                        $fillColor = 'FFFDA4AF'; // rose-300
                        $textColor = 'FF9F1239'; // rose-700
                    } elseif (strpos($statusCell, 'early checkout') !== false) {
                        $fillColor = 'FFFDE68A'; // yellow-300
                        $textColor = 'FF92400E'; // amber-600
                    } elseif (strpos($statusCell, 'telat') !== false) {
                        $fillColor = 'FFFED7AA'; // orange-200
                        $textColor = 'FF9A3412'; // orange-800
                    } elseif (strpos($statusCell, 'hadir') !== false) {
                        $fillColor = 'FFBBF7D0'; // green-200
                        $textColor = 'FF166534'; // green-800
                    } elseif (strpos($statusCell, 'izin') !== false) {
                        $fillColor = 'FFFEF08A'; // yellow-200
                        $textColor = 'FFA16207'; // amber-700
                    } elseif (strpos($statusCell, 'cuti') !== false) {
                        $fillColor = 'FFE9D5FF'; // purple-200
                        $textColor = 'FF7E22CE'; // purple-700
                    } elseif (strpos($statusCell, 'alpha') !== false) {
                        $fillColor = 'FFFECACA'; // red-200
                        $textColor = 'FF991B1B'; // red-700
                    }

                    if ($fillColor) {
                        $style = $sheet->getStyle("H{$row}");
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
