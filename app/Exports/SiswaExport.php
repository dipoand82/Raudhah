<?php

namespace App\Exports;

use App\Models\Siswa;
// Ubah FromCollection menjadi FromQuery
use Maatwebsite\Excel\Concerns\FromQuery; 
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SiswaExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $kelas_id;
    protected $status;
    private $rowNumber = 0; // Tetap dipertahankan untuk penomoran

    public function __construct($kelas_id = null, $status = null)
    {
        $this->kelas_id = $kelas_id;
        $this->status = $status;
    }

    /**
     * MENGGANTI function collection() MENJADI query()
     * Agar kita bisa menggunakan JOIN dan ORDER BY relasi
     */
    public function query()
    {
        $query = Siswa::query()
            // 1. JOIN (Agar bisa urutkan berdasarkan Nama User & Tingkat Kelas)
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->leftJoin('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            
            // 2. SELECT (Penting! Agar data ID siswa tidak tertimpa ID user/kelas)
            ->select('siswas.*');

        // Filter Kelas (Gunakan 'siswas.kelas_id' karena ada join)
        if ($this->kelas_id) {
            $query->where('siswas.kelas_id', $this->kelas_id);
        }

        // Filter Status
        if ($this->status) {
            $query->where('siswas.status', $this->status);
        }

        // 3. ORDER BY (Logika Pengurutan: Tingkat -> Kelas -> Nama)
        return $query
            ->orderBy('kelas.tingkat', 'asc')     // Urutkan Tingkat (7, 8, 9)
            ->orderBy('kelas.nama_kelas', 'asc')  // Urutkan Kelas (A, B, C)
            ->orderBy('users.name', 'asc')        // Urutkan Nama Siswa (A-Z)
            
            // Tetap load relasi agar hemat query saat mapping
            ->with(['user', 'kelas']);
    }

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

    public function map($siswa): array
    {
        $this->rowNumber++; // Counter nomor urut

        return [
            $this->rowNumber, 
            $siswa->user->name ?? '-',
            $siswa->nisn ?? '-',
            $siswa->kelas ? $siswa->kelas->nama_lengkap : '-', 
            $siswa->jenis_kelamin ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style tetap sama persis dengan yang lama
        $lastRow = $sheet->getHighestRow();
        $rangeTabel = 'A1:E' . $lastRow; // A sampai E

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