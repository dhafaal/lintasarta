<?php

namespace App\Exports;

use App\Models\Schedules;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

class ScheduleReportExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $month;
    protected $year;
    protected $daysInMonth;
    protected $users;
    protected $grandTotalHours = 0;

    public function __construct($month, $year)
    {
        $this->month       = $month;
        $this->year        = $year;
        $this->daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        Carbon::setLocale('id');

        $this->users = User::whereHas('schedules', function ($q) {
                $q->whereYear('schedule_date', $this->year)
                  ->whereMonth('schedule_date', $this->month);
            })
            ->whereIn('role', ['user', 'operator'])
            ->get();
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->users as $index => $user) {
            [$rowShift, $rowHours, $totalHours] = $this->generateUserRows($user, $index);
            $this->grandTotalHours += $totalHours;

            $data[] = $rowShift;
            $data[] = $rowHours;
        }

        $data[] = [];
        $data[] = [
            'NO'   => '',
            'NAMA' => 'TOTAL JAM KERJA SEMUA PEGAWAI',
            'REKAP' => $this->grandTotalHours . 'j'
        ];

        return $data;
    }

    private function generateUserRows($user, $index): array
    {
        $rowShift = ['NO' => $index + 1, 'NAMA' => $user->name];
        $rowHours = ['NO' => '', 'NAMA' => 'JAM KERJA'];
        $totalMinutes = 0;

        for ($day = 1; $day <= $this->daysInMonth; $day++) {
            $date = Carbon::createFromDate($this->year, $this->month, $day)->format('Y-m-d');
            $schedule = Schedules::with('shift')->where('user_id', $user->id)->whereDate('schedule_date', $date)->first();

            if ($schedule && $schedule->shift) {
                $start = Carbon::parse($schedule->shift->start_time);
                $end   = Carbon::parse($schedule->shift->end_time);
                if ($end->lt($start)) $end->addDay();

                $minutes = $start->diffInMinutes($end);
                $totalMinutes += $minutes;

                $shiftCode = strtoupper(substr($schedule->shift->shift_name, 0, 1));
                $rowShift[$day] = $shiftCode;
                $rowHours[$day] = floor($minutes / 60) . 'j';
            } else {
                $rowShift[$day] = '';
                $rowHours[$day] = '';
            }
        }

        $rowShift['TOTAL JAM'] = '';
        $rowHours['TOTAL JAM'] = round($totalMinutes / 60, 1) . 'j';

        return [$rowShift, $rowHours, round($totalMinutes / 60, 1)];
    }

    public function headings(): array
    {
        $monthName = Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
        $headings = [
            ["PT. APLIKANUSA LINTASARTA"],
            ["LAPORAN JADWAL KERJA PEGAWAI"],
            ["Periode: {$monthName}"],
            [],
        ];

        $header = array_merge(['NO', 'NAMA'], range(1, $this->daysInMonth), ['TOTAL JAM']);
        $headings[] = $header;

        $mapHari = ['Monday'=>'Sen','Tuesday'=>'Sel','Wednesday'=>'Rab','Thursday'=>'Kam','Friday'=>'Jum','Saturday'=>'Sab','Sunday'=>'Min'];
        $dayNames = array_merge(['',''], array_map(function($d) use ($mapHari) {
            return $mapHari[Carbon::createFromDate($this->year, $this->month, $d)->format('l')];
        }, range(1, $this->daysInMonth)), ['']);
        $headings[] = $dayNames;

        return $headings;
    }

    public function title(): string
    {
        return 'Jadwal';
    }

    public function styles(Worksheet $sheet)
    {
        $highestCol     = $sheet->getHighestColumn();
        $highestRow     = $sheet->getHighestRow();
        $colCount       = Coordinate::columnIndexFromString($highestCol);
        $dataStartRow   = 6;

        $this->styleHeaderLaporan($sheet, $highestCol);
        $this->styleHeaderTabel($sheet, $highestCol);
        $this->styleData($sheet, $dataStartRow, $highestRow, $colCount, $highestCol);
        $this->styleRekapTotal($sheet, $highestRow);

        // Adjusted column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(6); // Slightly wider for NO
        $sheet->getColumnDimension('B')->setWidth(35); // Wider for NAMA
        for ($i = 3; $i < $colCount; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(4.5); // Slightly narrower for days
        }
        $sheet->getColumnDimension($highestCol)->setWidth(12); // Adjusted for TOTAL JAM

        $sheet->freezePane('C6');
        return [];
    }

    private function styleHeaderLaporan(Worksheet $sheet, $highestCol)
    {
        // More formal colors and fonts
        $titles = [
            ['row' => 1, 'size' => 16, 'color' => 'FF003087', 'bold' => true], // Dark blue for company name
            ['row' => 2, 'size' => 14, 'color' => 'FF003087', 'bold' => true], // Consistent dark blue for report title
            ['row' => 3, 'size' => 11, 'color' => 'FF333333', 'italic' => true], // Dark gray for period
        ];

        foreach ($titles as $t) {
            $sheet->mergeCells("A{$t['row']}:{$highestCol}{$t['row']}");
            $sheet->getStyle("A{$t['row']}")->applyFromArray([
                'font' => [
                    'name' => 'Arial', // Professional font
                    'size' => $t['size'],
                    'bold' => $t['bold'] ?? false,
                    'italic' => $t['italic'] ?? false,
                    'color' => ['argb' => $t['color']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }
    }

    private function styleHeaderTabel(Worksheet $sheet, $highestCol)
    {
        // Subtle gray header with white text
        $sheet->getStyle("A5:{$highestCol}6")->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'bold' => true,
                'size' => 10,
                'color' => ['argb' => 'FFFFFFFF'], // White text
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4B5EAA'], // Professional dark blue-gray
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM, // Medium borders for prominence
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
            ],
        ]);
    }

    private function styleData(Worksheet $sheet, $startRow, $highestRow, $colCount, $highestCol)
    {
        // Consistent font and cleaner borders
        $sheet->getStyle("A{$startRow}:{$highestCol}{$highestRow}")->applyFromArray([
            'font' => [
                'name' => 'Geist',
                'size' => 10,
                'color' => ['argb' => 'FF1A1A1A'], // Darker gray for text
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Thin borders for clean look
                    'color' => ['argb' => 'FF999999'], // Light gray borders
                ],
            ],
        ]);
        $sheet->getStyle("B{$startRow}:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Subtle zebra stripes
        for ($row = $startRow; $row <= $highestRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$highestCol}{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8F9FA'); // Very light gray for zebra
            }
        }

        // Refined shift colors
        for ($row = $startRow; $row <= $highestRow; $row += 2) {
            for ($i = 3; $i < $colCount; $i++) {
                $col = Coordinate::stringFromColumnIndex($i);
                $val = $sheet->getCell($col . $row)->getValue();
                if ($val === 'P') {
                    $this->applyShiftStyle($sheet, $col . $row, 'FF003087', 'FFE6EEFF'); // Soft blue
                } elseif ($val === 'S') {
                    $this->applyShiftStyle($sheet, $col . $row, 'FF2E7D32', 'FFE8F5E9'); // Soft green
                } elseif ($val === 'M') {
                    $this->applyShiftStyle($sheet, $col . $row, 'FF8E24AA', 'FFF3E8FD'); // Soft purple
                }
            }
        }

        // Total column styling
        $sheet->getStyle("{$highestCol}{$startRow}:{$highestCol}{$highestRow}")->applyFromArray([
            'font' => [
                'name' => 'Geist',
                'bold' => true,
                'color' => ['argb' => 'FF003087'], // Dark blue for totals
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE6EEFF'], // Light blue background
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF4B5EAA'], // Blue-gray border
                ],
            ],
        ]);
    }

    private function styleRekapTotal(Worksheet $sheet, $rekapRow)
    {
        // Formal total row styling
        $sheet->mergeCells("A{$rekapRow}:B{$rekapRow}");
        $sheet->getStyle("A{$rekapRow}:C{$rekapRow}")->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // White text
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4B5EAA'], // Dark blue-gray
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
            ],
        ]);
    }

    private function applyShiftStyle(Worksheet $sheet, string $cell, string $fontColor, string $bgColor): void
    {
        $sheet->getStyle($cell)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'color' => ['argb' => $fontColor],
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $bgColor],
            ],
        ]);
    }
}