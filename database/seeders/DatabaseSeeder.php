<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. BUAT AKUN USER (3 Role Saja)
        // ==========================================
        
        // Akun Admin (Tata Usaha) - ID 1
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Staff Tata Usaha',
            'username' => 'admin',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Akun Guru (Kepala Sekolah) - ID 2
        $guruId = DB::table('users')->insertGetId([
            'name' => 'Bapak Kepala Sekolah',
            'username' => 'kepsek',
            'email' => 'kepsek@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Akun Siswa (Budi) - ID 3
        $siswaUserId = DB::table('users')->insertGetId([
            'name' => 'Budi Santoso',
            'username' => 'budi123',
            'email' => 'budi@siswa.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ==========================================
        // 2. DATA MASTER AKADEMIK & KEUANGAN
        // ==========================================

        // Tahun Ajaran
        $tahunId = DB::table('tahun_ajarans')->insertGetId([
            'tahun' => '2024/2025',
            'semester' => 'ganjil',
            'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Kelas VII A
        $kelasId = DB::table('kelas')->insertGetId([
            'nama_kelas' => 'VII A',
            'tingkat' => 7,
            'kode_kelas' => '7A',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Jenis Tagihan (SPP)
        $masterSppId = DB::table('master_tagihans')->insertGetId([
            'nama_tagihan' => 'SPP Bulanan',
            'nominal' => 150000,
            'deskripsi' => 'Pembayaran Wajib Bulanan',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ==========================================
        // 3. DATA SISWA & RIWAYAT (Inti SPP)
        // ==========================================

        // Profil Siswa Budi
        $siswaId = DB::table('siswas')->insertGetId([
            'user_id' => $siswaUserId,
            'tahun_masuk_id' => $tahunId,
            'nisn' => '1234567890',
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Merpati No. 10, Bandar Lampung',
            'no_telp_wali' => '081234567890',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Riwayat Budi masuk Kelas VII A
        $riwayatId = DB::table('riwayat_akademiks')->insertGetId([
            'siswa_id' => $siswaId,
            'kelas_id' => $kelasId,
            'tahun_ajaran_id' => $tahunId,
            'status_siswa' => 'aktif',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ==========================================
        // 4. TRANSAKSI (Tagihan & Pembayaran)
        // ==========================================

        // Tagihan Juli (Belum Lunas) - Buat ngetes fitur bayar
        DB::table('tagihan_spps')->insert([
            'master_tagihan_id' => $masterSppId,
            'riwayat_akademik_id' => $riwayatId,
            'bulan' => 'Juli',
            'tahun' => '2024',
            'jumlah_tagihan' => 150000,
            'status' => 'belum_lunas',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Tagihan Agustus (Lunas) - Buat ngetes histori
        $tagihanLunasId = DB::table('tagihan_spps')->insertGetId([
            'master_tagihan_id' => $masterSppId,
            'riwayat_akademik_id' => $riwayatId,
            'bulan' => 'Agustus',
            'tahun' => '2024',
            'jumlah_tagihan' => 150000,
            'status' => 'lunas',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Pembayaran untuk Agustus
        DB::table('pembayarans')->insert([
            'tagihan_spp_id' => $tagihanLunasId,
            'user_id_admin' => $adminId, // Diverifikasi Admin
            'kode_pembayaran' => 'INV-20240801-001',
            'jumlah_bayar' => 150000,
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'tunai',
            'status_gateway' => 'success',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ==========================================
        // 5. DATA PROFIL SEKOLAH (Baru!)
        // ==========================================

        // Pengaturan Dasar
        DB::table('pengaturans')->insert([
            ['kunci' => 'nama_sekolah', 'nilai' => 'SMP IT Raudhah', 'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'alamat_sekolah', 'nilai' => 'Jl. Pendidikan No. 1, Kota', 'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'visi', 'nilai' => 'Mewujudkan Generasi Cerdas & Berakhlak', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Contoh Artikel / Berita
        DB::table('artikels')->insert([
            'user_id' => $adminId, // Admin yang nulis
            'judul' => 'Penerimaan Siswa Baru 2025',
            'slug' => 'penerimaan-siswa-baru-2025',
            'isi' => 'Telah dibuka penerimaan siswa baru untuk tahun ajaran mendatang. Silakan daftar segera!',
            'gambar' => 'default_berita.jpg',
            'tanggal_publish' => now(),
            'status' => 'published',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Contoh Galeri / Dokumentasi
        DB::table('galeris')->insert([
            'user_id' => $adminId, // Admin yang upload
            'judul' => 'Kegiatan Upacara Senin',
            'deskripsi' => 'Dokumentasi upacara bendera rutin.',
            'gambar' => 'default_galeri.jpg',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}