<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $kelas_id;

    protected $status;

    private $rowNumber = 0;

    public function __construct($kelas_id = null, $status = null)
    {
        $this->kelas_id = $kelas_id;
        $this->status = $status;
    }

    public function query()
    {
        $query = Siswa::query()
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->leftJoin('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            ->select('siswas.*');

        if ($this->kelas_id) {
            $query->where('siswas.kelas_id', $this->kelas_id);
        }

        if ($this->status) {
            $query->where('siswas.status', $this->status);
        }

        return $query
            ->orderBy('kelas.tingkat', 'asc')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('users.name', 'asc')
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
        $this->rowNumber++;

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
        $lastRow = $sheet->getHighestRow();
        $rangeTabel = 'A1:E'.$lastRow;
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
