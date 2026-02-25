<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanPembayaranExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Rekap ' . ucfirst($this->data['periode']) . ' ' . $this->data['tahun'];
    }

    public function view(): View
    {
        return view('admin.keuangan.laporan.export_excel', $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        $masterTagihans = $this->data['masterTagihans'];
        $bulanList      = $this->data['bulanList'];
        $siswas         = $this->data['siswas'];

        // Hitung posisi kolom
        $totalKolom = 2; // No + Nama
        $sppStartKolom = 2; // akan diupdate saat ketemu SPP
        $sppKolom = 0;
        $nonSppKolom = 0;

        foreach ($masterTagihans as $mt) {
            $isSPP = stripos($mt->nama_tagihan, 'spp') !== false;
            if (!$isSPP) {
                $nonSppKolom++;
                $totalKolom++;
            }
        }
        $sppStartKolom = 2 + $nonSppKolom + 1; // kolom mulai SPP (1-based)
        foreach ($masterTagihans as $mt) {
            $isSPP = stripos($mt->nama_tagihan, 'spp') !== false;
            if ($isSPP) {
                $sppKolom += count($bulanList);
                $totalKolom += count($bulanList);
            }
        }
        $totalKolom++; // kolom Total

        $lastCol     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalKolom);
        $sppStartCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sppStartKolom);
        $sppEndCol   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sppStartKolom + $sppKolom - 1);
        $lastRow     = $siswas->count() + 3; // 2 header + data + 1 total
        $fullRange   = "A1:{$lastCol}{$lastRow}";

        return [


            // 2. Seluruh header (baris 1-2) — abu gelap seperti template
            "A1:{$lastCol}2" => [
                'font' => ['bold' => true, 'color' => ['rgb' => '333333'], 'size' => 9],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9'], // abu muda seperti template
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
            ],

            // 3. Header SPP — kuning seperti template
            "{$sppStartCol}1:{$sppEndCol}2" => [
                'font' => ['bold' => true, 'color' => ['rgb' => '333333'], 'size' => 9],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2CC'], // kuning template
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],

            // 4. Header kolom TOTAL — biru seperti template
            "{$lastCol}1:{$lastCol}2" => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'], // biru excel klasik
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],

            // 5. Baris data — putih, font normal
            "A3:{$lastCol}" . ($lastRow - 1) => [
                'font'      => ['bold' => false, 'size' => 9, 'color' => ['rgb' => '333333']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],

            // 6. Kolom No (A) — center
            "A3:A{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],

            // 7. Kolom Nama (B) — kiri
            "B3:B{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],

            // 8. Kolom data angka — rata kanan
            "C3:{$lastCol}" . ($lastRow - 1) => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],

            // 9. Kolom TOTAL data (kanan) — biru muda
            "{$lastCol}3:{$lastCol}" . ($lastRow - 1) => [
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DCE6F1'], // biru muda excel
                ],
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1F3864']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],

            // 10. Baris TOTAL (footer) — kuning bold seperti template
            "A{$lastRow}:{$lastCol}{$lastRow}" => [
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '333333']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2CC'], // kuning sama seperti header SPP
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],

            // 11. Label TOTAL kiri di footer
            "A{$lastRow}:B{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '333333']],
            ],

            // 12. Grand total pojok kanan bawah — biru gelap
            "{$lastCol}{$lastRow}" => [
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],

            // 13. Border outline + allBorders (dipanggil terakhir agar tidak tertimpa)
            $fullRange => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color'       => ['rgb' => '999999'],
                    ],
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'BBBBBB'],
                    ],
                ],
            ],
        ];
    }
}