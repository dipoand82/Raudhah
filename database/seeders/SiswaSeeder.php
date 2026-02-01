<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. AMBIL DATA PENDUKUNG (Master Data)
        // Kita butuh ID Tahun Ajaran Aktif & ID Kelas biar relasinya nyambung
        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        
        // Ambil Kelas 7A (Pastikan di DatabaseSeeder sudah dibuat)
        $kelas7A = Kelas::where('tingkat', 7)->where('nama_kelas', 'A')->first();
        
        // Ambil Kelas 8A (Kita buat dummy kalau belum ada, atau pakai 7A lagi gapapa)
        $kelasTarget = $kelas7A; 

        // Pastikan data master ada biar gak error
        if (!$tahunAktif || !$kelasTarget) {
            $this->command->info('Tahun Ajaran atau Kelas belum ada. Jalankan DatabaseSeeder dulu!');
            return;
        }

        // ==========================================
        // SISWA 1: Eman Santoso
        // ==========================================
        
        // A. Buat Akun Login
        $userEman = User::create([
            'name' => 'Eman Santoso',
            'email' => '0032190551@raudhah.com', // Pakai format email dummy sekolah
            'password' => Hash::make('123456'),
            'role' => 'siswa',
            'must_change_password' => true,
        ]);

        // B. Buat Data Siswa
        Siswa::create([
            'user_id' => $userEman->id,
            'nisn' => '0032190551',
            'nama_lengkap' => 'Eman Santoso',
            'jenis_kelamin' => 'L', // Enum: L atau P
            
            // Data Akademik
            'kelas_id' => $kelasTarget->id,
            'tingkat' => $kelasTarget->tingkat,
            'tahun_ajaran_id' => $tahunAktif->id,
            'status' => 'Aktif',
        ]);

        // ==========================================
        // SISWA 2: Ani Suryani
        // ==========================================

        // A. Buat Akun Login
        $userAni = User::create([
            'name' => 'Ani Suryani',
            'email' => '0087654321@raudhah.com',
            'password' => Hash::make('123456'),
            'role' => 'siswa',
            'must_change_password' => true,
        ]);

        // B. Buat Data Siswa
        Siswa::create([
            'user_id' => $userAni->id,
            'nisn' => '0087654321',
            'nama_lengkap' => 'Ani Suryani',
            'jenis_kelamin' => 'P', // Enum: L atau P
            
            // Data Akademik
            'kelas_id' => $kelasTarget->id,
            'tingkat' => $kelasTarget->tingkat,
            'tahun_ajaran_id' => $tahunAktif->id,
            'status' => 'Aktif',
        ]);
    }
}
