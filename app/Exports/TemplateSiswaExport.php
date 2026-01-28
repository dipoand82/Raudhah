<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TemplateSiswaExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * 1. Contoh Data (Dummy)
     * Menambahkan angka di index pertama tiap array.
     */
    public function array(): array
    {
        return [
            ['1', 'Budi Santoso', '00123456', '7A', 'L'],
            ['2', 'Siti Aminah', '00123457', '8B', 'P'],
        ];
    }

    /**
     * 2. Judul Kolom (HEADER)
     * Menambahkan kolom 'No' di posisi awal.
     */
    public function headings(): array
    {
        return [
            'No', // Tambahan kolom penomoran
            'nama_lengkap',
            'nisn',
            'kelas',
            'jenis_kelamin',
        ];
    }

    /**
     * 3. Styling Header & Konten
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Header)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF'] 
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0A78BD'] 
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Border diperluas ke kolom E (A1:E3) karena ada kolom tambahan
            'A1:E3' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            // Opsional: Mengetengahkan kolom "No" dan "Jenis Kelamin" agar lebih rapi
            'A2:A3' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'E2:E3' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }
}
