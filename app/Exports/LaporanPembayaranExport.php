<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPembayaranExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Rekap '.ucfirst($this->data['periode']).' '.$this->data['tahun'];
    }

    public function view(): View
    {
        return view('admin.keuangan.laporan.export_excel', $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        $masterTagihans = $this->data['masterTagihans'];
        $bulanList = $this->data['bulanList'];
        $siswas = $this->data['siswas'];
        $totalKolom = 2;
        $sppStartKolom = 2;
        $sppKolom = 0;
        $nonSppKolom = 0;

        foreach ($masterTagihans as $mt) {
            $isSPP = stripos($mt->nama_tagihan, 'spp') !== false;
            if (! $isSPP) {
                $nonSppKolom++;
                $totalKolom++;
            }
        }
        $sppStartKolom = 2 + $nonSppKolom + 1;
        foreach ($masterTagihans as $mt) {
            $isSPP = stripos($mt->nama_tagihan, 'spp') !== false;
            if ($isSPP) {
                $sppKolom += count($bulanList);
                $totalKolom += count($bulanList);
            }
        }
        $totalKolom++;

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalKolom);
        $sppStartCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sppStartKolom);
        $sppEndCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sppStartKolom + $sppKolom - 1);
        $lastRow = $siswas->count() + 3;
        $fullRange = "A1:{$lastCol}{$lastRow}";

        return [

            "A1:{$lastCol}2" => [
                'font' => ['bold' => true, 'color' => ['rgb' => '333333'], 'size' => 9],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],

            "{$sppStartCol}1:{$sppEndCol}2" => [
                'font' => ['bold' => true, 'color' => ['rgb' => '333333'], 'size' => 9],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2CC'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            "{$lastCol}1:{$lastCol}2" => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],

            "A3:{$lastCol}".($lastRow - 1) => [
                'font' => ['bold' => false, 'size' => 9, 'color' => ['rgb' => '333333']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],

            "A3:A{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],

            "B3:B{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],

            "C3:{$lastCol}".($lastRow - 1) => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],

            "{$lastCol}3:{$lastCol}".($lastRow - 1) => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DCE6F1'],
                ],
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1F3864']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],

            "A{$lastRow}:{$lastCol}{$lastRow}" => [
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '333333']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2CC'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],

            "A{$lastRow}:B{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '333333']],
            ],

            "{$lastCol}{$lastRow}" => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],

            $fullRange => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '999999'],
                    ],
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'BBBBBB'],
                    ],
                ],
            ],
        ];
    }
}
