<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Shift;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ScheduleTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        // Return contoh data dengan format baru
        // Untuk mengisi 2 shift di 1 tanggal, gunakan 2 baris dengan tanggal yang sama
        return collect([
            // Contoh 1: User 3, tanggal 23, hanya 1 shift (Shift Malam)
            [
                'user_id' => '3',
                'tanggal' => '23',
                'shift_id' => '3',
            ],
            // Contoh 2: User 3, tanggal 24, 2 shift (Pagi dan Siang)
            [
                'user_id' => '3',
                'tanggal' => '24',
                'shift_id' => '1',
            ],
            [
                'user_id' => '3',
                'tanggal' => '24',
                'shift_id' => '2',
            ],
            // Contoh 3: User 5, tanggal 25, hanya 1 shift (Shift Pagi)
            [
                'user_id' => '5',
                'tanggal' => '25',
                'shift_id' => '1',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'user_id',
            'tanggal',
            'shift_id',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0EA5E9'], // Sky blue
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
            // Style untuk data rows
            'A2:C100' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // user_id
            'B' => 12, // tanggal
            'C' => 15, // shift_id
        ];
    }
}
