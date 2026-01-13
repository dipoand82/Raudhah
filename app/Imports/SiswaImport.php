<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. CARI ID KELAS BERDASARKAN KODE/NAMA DI EXCEL
        // Pastikan di Excel kolom 'kelas' isinya: "7A", "VII-A", atau sesuai nama kelas di DB
        $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();
        
        // 2. CARI TAHUN MASUK YANG AKTIF (Otomatis set ke tahun aktif sekarang)
        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        // 3. BUAT AKUN LOGIN (USER)
        $user = User::create([
            'name'     => $row['nama'],  // Kolom Excel: nama
            'email'    => $row['email'], // Kolom Excel: email
            'role'     => 'siswa',
            'password' => Hash::make('12345678'),
            'must_change_password' => true,
        ]);

        // 4. BUAT PROFIL SISWA
        return new Siswa([
            'user_id'        => $user->id,
            'kelas_id'       => $kelas ? $kelas->id : null, // Kalau kelas gak ketemu, biarkan kosong
            'tahun_masuk_id' => $tahunAktif ? $tahunAktif->id : null,
            'nisn'           => $row['nisn'],
            'jenis_kelamin'  => $row['jk'], // Excel: 'L' atau 'P'
            'status'         => 'Aktif',
        ]);
    }
}