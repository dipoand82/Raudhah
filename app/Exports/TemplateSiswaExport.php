<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TemplateSiswaExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * 1. Contoh Data (Dummy)
     * Membantu Admin memahami format pengisian data siswa.
     */
    public function array(): array
    {
        return [
            ['Budi Santoso', '00123456', '7A', 'L'],
            ['Siti Aminah', '00123457', '8B', 'P'],
        ];
    }

    /**
     * 2. Judul Kolom (HEADER)
     * Nama kolom harus sesuai dengan yang dibaca oleh SiswaImport.
     */
    public function headings(): array
    {
        return [
            'nama_lengkap',
            'nisn',
            'kelas',
            'jenis_kelamin',
        ];
    }

    /**
     * 3. Styling Header & Konten
     * Memberikan warna biru #0A78BD pada header agar senada dengan sistem.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Header)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF'] // Teks Putih
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0A78BD'] // Biru Identitas SMP IT Raudhah
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Memberikan border tipis pada data dummy (Baris 2 dan 3)
            'A1:D3' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}