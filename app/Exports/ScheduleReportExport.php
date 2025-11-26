<?php

namespace App\Exports;

use App\Services\ScheduleMonthlyDataBuilder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduleReportExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $month;
    protected $year;
    protected $daysInMonth;
    protected $data = [];
    protected $grandTotalHours = 0;

    public function __construct($month, $year)
    {
        $this->month       = $month;
        $this->year        = $year;
        $this->daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        Carbon::setLocale('id');
        [$this->data, $this->daysInMonth] = ScheduleMonthlyDataBuilder::build($this->month, $this->year);
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->data as $index => $row) {
            $rowShift = ['NO' => $index + 1, 'NAMA' => $row['nama']];
            $rowHours = ['NO' => '', 'NAMA' => 'JAM KERJA'];

            for ($day = 1; $day <= $this->daysInMonth; $day++) {
                $dayData = $row['shifts'][$day] ?? null;
                $rowShift[$day] = ($dayData && $dayData['shift_name']) ? $dayData['shift_name'] : '-';
                $rowHours[$day] = ($dayData && $dayData['hours']) ? $dayData['hours'] : '-';
            }

            $rowShift['TOTAL JAM'] = '';
            $rowHours['TOTAL JAM'] = $row['total_jam'];

            $data[] = $rowShift;
            $data[] = $rowHours;

            $this->grandTotalHours += (float) str_replace('j', '', $row['total_jam']);
        }

        $data[] = [];
        $totalHoursFormatted = ($this->grandTotalHours == floor($this->grandTotalHours))
            ? floor($this->grandTotalHours) . 'j'
            : number_format($this->grandTotalHours, 1) . 'j';

        $data[] = [
            'NO'   => '',
            'NAMA' => 'TOTAL JAM KERJA SEMUA PEGAWAI',
            'REKAP' => $totalHoursFormatted
        ];

        return $data;
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

        // Adjusted column widths untuk shift 1 dan shift 2
        $sheet->getColumnDimension('A')->setWidth(6); // NO
        $sheet->getColumnDimension('B')->setWidth(35); // NAMA
        for ($i = 3; $i < $colCount; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(18); // Lebih lebar untuk shift ganda
        }
        $sheet->getColumnDimension($highestCol)->setWidth(15); // TOTAL JAM

        // Set row height untuk mengakomodasi shift ganda
        for ($row = $dataStartRow; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(35); // Lebih tinggi untuk shift ganda
        }

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
                'name' => 'Arial',
                'size' => 9,
                'color' => ['argb' => 'FF1A1A1A'], // Darker gray for text
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true, // Wrap text untuk nama shift panjang
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

        // Shift colors sesuai dengan calendar
        for ($row = $startRow; $row <= $highestRow; $row += 2) {
            for ($i = 3; $i < $colCount; $i++) {
                $col = Coordinate::stringFromColumnIndex($i);
                $val = $sheet->getCell($col . $row)->getValue();

                // Deteksi berdasarkan nama shift lengkap (termasuk shift ganda)
                if (stripos($val, 'Pagi') !== false || stripos($val, 'Morning') !== false) {
                    // Jika ada shift ganda dengan Pagi, gunakan warna campuran
                    if (strpos($val, '+') !== false) {
                        $this->applyShiftStyle($sheet, $col . $row, 'FF1E40AF', 'FFE0E7FF'); // Darker blue untuk shift ganda
                    } else {
                        $this->applyShiftStyle($sheet, $col . $row, 'FF3B82F6', 'FFDBEAFE'); // Blue - Pagi
                    }
                } elseif (stripos($val, 'Siang') !== false || stripos($val, 'Day') !== false || stripos($val, 'Afternoon') !== false) {
                    if (strpos($val, '+') !== false) {
                        $this->applyShiftStyle($sheet, $col . $row, 'FFB45309', 'FFFEF3C7'); // Darker yellow untuk shift ganda
                    } else {
                        $this->applyShiftStyle($sheet, $col . $row, 'FFEAB308', 'FFFEF3C7'); // Yellow - Siang
                    }
                } elseif (stripos($val, 'Malam') !== false || stripos($val, 'Night') !== false) {
                    if (strpos($val, '+') !== false) {
                        $this->applyShiftStyle($sheet, $col . $row, 'FF7C3AED', 'FFF3E8FF'); // Darker purple untuk shift ganda
                    } else {
                        $this->applyShiftStyle($sheet, $col . $row, 'FFA855F7', 'FFF3E8FF'); // Purple - Malam
                    }
                } elseif (stripos($val, 'Alpha') !== false) {
                    // Status Alpha - warna merah
                    $this->applyShiftStyle($sheet, $col . $row, 'FFDC2626', 'FFFEF2F2'); // Red - Alpha
                } elseif (stripos($val, 'Cuti') !== false) {
                    // Status Cuti - warna purple
                    $this->applyShiftStyle($sheet, $col . $row, 'FF7C3AED', 'FFF3E8FF'); // Purple - Cuti
                } elseif (stripos($val, 'Izin') !== false || stripos($val, 'Sakit') !== false) {
                    // Status Izin/Sakit - warna amber
                    $this->applyShiftStyle($sheet, $col . $row, 'FFEAB308', 'FFFEF3C7'); // Amber - Izin/Sakit
                } elseif ($val !== '-' && $val !== '') {
                    // Untuk shift ganda dengan kombinasi lain
                    if (strpos($val, '+') !== false) {
                        $this->applyShiftStyle($sheet, $col . $row, 'FF374151', 'FFF3F4F6'); // Dark gray untuk shift ganda lainnya
                    } else {
                        $this->applyShiftStyle($sheet, $col . $row, 'FF6B7280', 'FFF9FAFB'); // Gray - Shift lainnya
                    }
                }
            }
        }

        // Total column styling
        $sheet->getStyle("{$highestCol}{$startRow}:{$highestCol}{$highestRow}")->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'bold' => true,
                'size' => 10,
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
