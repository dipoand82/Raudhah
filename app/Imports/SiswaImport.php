<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    // Tambahkan properti untuk menyimpan tahun aktif agar tidak query berulang kali
    protected $tahunAktif;

    public function __construct()
    {
        // Ambil tahun aktif sekali saja saat class dibuat
        $this->tahunAktif = TahunAjaran::where('is_active', true)->first();
    }

    /**
     * 1. ATURAN VALIDASI DATA EXCEL
     */
    public function rules(): array
    {
        return [
            'nisn' => 'required|numeric',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P,l,p',
            'kelas' => 'required',
        ];
    }

    public function model(array $row)
    {
        // 1. LOGIKA PEMECAH KELAS (Tetap Menggunakan Regex Anda)
        $kelas_id = null;
        $tingkat = 7; 

        if (isset($row['kelas'])) {
            $kelasRaw = strtoupper(str_replace(' ', '', $row['kelas'])); 
            preg_match('/^\d+/', $kelasRaw, $matchesTingkat);
            $tingkat = $matchesTingkat[0] ?? 7;
            $namaKelas = str_replace($tingkat, '', $kelasRaw); 

            $dataKelas = Kelas::where('tingkat', $tingkat)
                              ->where('nama_kelas', $namaKelas)
                              ->first();
            
            if ($dataKelas) {
                $kelas_id = $dataKelas->id;
            }
        }

        // 2. BUAT/UPDATE USER (AKUN LOGIN)
        $emailDummy = $row['nisn'] . '@student.sekolah.id';
        
        $user = User::updateOrCreate(
            ['email' => $emailDummy],
            [
                'name' => $row['nama_lengkap'],
                'password' => Hash::make('123456'), 
                'role' => 'siswa',
                'must_change_password' => true,
            ]
        );

        // 3. BUAT/UPDATE DATA SISWA (LOGIKA KENAIKAN KELAS)
        // Jika NISN sudah ada, kolom kelas_id akan diperbarui sesuai Excel baru
        return Siswa::updateOrCreate(
            ['nisn' => $row['nisn']],
            [
                'user_id'         => $user->id,
                'nama_lengkap'    => $row['nama_lengkap'],
                'jenis_kelamin'   => strtoupper($row['jenis_kelamin']), 
                'kelas_id'        => $kelas_id,
                'tingkat'         => $tingkat, 
                'tahun_ajaran_id' => $this->tahunAktif ? $this->tahunAktif->id : null,
                'status'          => 'Aktif',
            ]
        );
    }
}