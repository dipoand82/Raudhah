<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $kelas_id;
    protected $status;
    private $rowNumber = 0; // 1. TAMBAHKAN INI: Untuk menghitung nomor urut

    public function __construct($kelas_id = null, $status = null)
    {
        $this->kelas_id = $kelas_id;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Siswa::with(['user', 'kelas']);

        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    /**
     * 2. TAMBAHKAN 'NO' DI HEADINGS
     */
    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'NISN',
            'KELAS',
            'JENIS KELAMIN',
        ];
    }

    /**
     * 3. TAMBAHKAN LOGIKA NOMOR DI MAPPING
     */
    public function map($siswa): array
    {
        $this->rowNumber++; // Tambah angka 1 setiap baris baru

        return [
            $this->rowNumber, // Masukkan nomor ke kolom pertama
            $siswa->user->name ?? '-',
            $siswa->nisn ?? '-',
            $siswa->kelas ? $siswa->kelas->nama_lengkap : '-', 
            $siswa->jenis_kelamin ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        // Karena kolom nambah (NO), jangkauan border jadi A1 sampai E
        $rangeTabel = 'A1:E' . $lastRow;

        $sheet->getStyle($rangeTabel)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);

        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}