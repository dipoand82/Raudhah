<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $kelas_id;
    protected $status; // <--- TAMBAHAN

    // Menerima filter kelas_id dari Controller
    public function __construct($kelas_id = null, $status = null)
    {
        $this->kelas_id = $kelas_id;
        $this->status = $status;
    }

    /**
     * MENGAMBIL DATA DARI DATABASE
     */
    public function collection()
    {
        // Ambil data siswa beserta relasi user dan kelas
        $query = Siswa::with(['user', 'kelas']);

        // Jika ada filter kelas, ambil kelas itu saja
        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    /**
     * JUDUL KOLOM (Harus sama persis dengan TemplateSiswaExport)
     * Agar saat diupload ulang, sistem bisa membacanya.
     */
    public function headings(): array
    {
        return [
            'nama_lengkap',
            'nisn',
            'kelas',         // Nanti isinya "7A", "8B", dll
            'jenis_kelamin',
        ];
    }

    /**
     * MAPPING DATA DATABASE KE KOLOM EXCEL
     */
    public function map($siswa): array
    {
        // Logic menggabungkan Tingkat + Nama Kelas (Misal: 7 + A = 7A)
        $namaKelas = $siswa->kelas ? ($siswa->kelas->tingkat . $siswa->kelas->nama_kelas) : '';

        return [
            $siswa->user->name ?? '', // Kolom nama_lengkap
            $siswa->nisn,             // Kolom nisn
            $namaKelas,               // Kolom kelas (Format: 7A)
            $siswa->jenis_kelamin,    // Kolom jenis_kelamin
        ];
    }

    /**
     * STYLING (BIAR SAMA KERENNYA DENGAN TEMPLATE ANDA)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header Biru (Baris 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0A78BD'] // Warna Biru Raudhah
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}