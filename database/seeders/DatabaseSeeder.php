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
        // 1. BUAT AKUN USER (3 Role)
        // ==========================================

        // Akun Admin (Tata Usaha)
        // ==========================================
        // 1. BUAT AKUN USER (4 Admin dengan Nama Umum)
        // ==========================================

        // Admin 1
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Staf Administrasi 1',
            'email' => 'admin11@raudhah.com',
            'password' => Hash::make('AdminRaudhah1*'),
            'role' => 'admin',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Admin 2
        DB::table('users')->insert([
            'name' => 'Staf Administrasi 2',
            'email' => 'admin22@raudhah.com',
            'password' => Hash::make('AdminRaudhah2*'),
            'role' => 'admin',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Admin 3
        DB::table('users')->insert([
            'name' => 'Staf Administrasi 3',
            'email' => 'admin33@raudhah.com',
            'password' => Hash::make('AdminRaudhah3*'),
            'role' => 'admin',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Admin 4
        DB::table('users')->insert([
            'name' => 'Staf Administrasi 4',
            'email' => 'admin44@raudhah.com',
            'password' => Hash::make('AdminRaudhah4*'),
            'role' => 'admin',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Akun Guru (Kepala Sekolah)
        $guruId = DB::table('users')->insertGetId([
            'name' => 'Bapak Kepala Sekolah',
            'email' => 'kepsek@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Akun Siswa (Budi)
        $siswaUserId = DB::table('users')->insertGetId([
            'name' => 'Budi Santoso',
            'email' => 'budi@siswa.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'must_change_password' => true, // Siswa wajib ganti password
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ==========================================
        // 2. DATA MASTER AKADEMIK & KEUANGAN
        // ==========================================

        // Tahun Ajaran (PERBAIKAN: Hapus kolom semester)
        $tahunId = DB::table('tahun_ajarans')->insertGetId([
            'tahun' => '2024/2025',
            // 'semester' => 'ganjil', <--- SUDAH DIHAPUS
            'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Kelas 7A (PERBAIKAN: Nama kelas cukup 'A', Tingkat '7')
        $kelasId = DB::table('kelas')->insertGetId([
            'nama_kelas' => 'A', // Cukup hurufnya saja
            'tingkat' => 7,      // Angkanya disini
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
        // 3. DATA SISWA & RIWAYAT
        // ==========================================

        // Profil Siswa Budi (PERBAIKAN: Isi kolom tingkat & tahun_ajaran_id)
        $siswaId = DB::table('siswas')->insertGetId([
            'user_id' => $siswaUserId,
            'tahun_ajaran_id' => $tahunId, // Penanda siswa aktif tahun ini
            'nisn' => '1234567890',
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'kelas_id' => $kelasId, // Masukkan ke kelas
            'tingkat' => 7,         // Set tingkat
            'status' => 'Aktif',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Riwayat Akademik (Opsional, untuk histori perpindahan kelas)
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

        // Tagihan Juli (Belum Lunas)
        DB::table('tagihan_spps')->insert([
            'master_tagihan_id' => $masterSppId,
            'riwayat_akademik_id' => $riwayatId,
            'bulan' => 'Juli',
            'tahun' => '2024',
            'jumlah_tagihan' => 150000,
            'status' => 'belum_lunas',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Tagihan Agustus (Lunas)
        $tagihanLunasId = DB::table('tagihan_spps')->insertGetId([
            'master_tagihan_id' => $masterSppId,
            'riwayat_akademik_id' => $riwayatId,
            'bulan' => 'Agustus',
            'tahun' => '2024',
            'jumlah_tagihan' => 150000,
            'status' => 'lunas',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // PERBAIKAN DI SINI: Sesuaikan dengan Migration Pembayarans terbaru kamu
        DB::table('pembayarans')->insert([
            'siswa_id' => $siswaId,                // SESUAI MIGRATION (Baris 14)
            'user_id_admin' => $adminId,           // SESUAI MIGRATION (Baris 16)
            'kode_pembayaran' => 'INV-20240801-001',
            'total_bayar' => 150000,               // UBAH dari 'jumlah_bayar' jadi 'total_bayar' (Baris 19)
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'tunai',
            'status_gateway' => 'success',         // Status default di migration kamu 'pending'
            'snap_token' => null,                  // SESUAI MIGRATION (Baris 23)
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ==========================================
        // 5. DATA PROFIL SEKOLAH
        // ==========================================

        DB::table('pengaturans')->insert([
            ['kunci' => 'nama_sekolah', 'nilai' => 'SMP IT Raudhah', 'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'alamat_sekolah', 'nilai' => 'Jl. Pendidikan No. 1, Kota', 'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'visi', 'nilai' => 'Mewujudkan Generasi Cerdas & Berakhlak', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('artikels')->insert([
            'user_id' => $adminId,
            'judul' => 'Penerimaan Siswa Baru 2025',
            'slug' => 'penerimaan-siswa-baru-2025',
            'isi' => 'Telah dibuka penerimaan siswa baru.',
            'gambar' => 'default_berita.jpg',
            'tanggal_publish' => now(),
            'status' => 'published',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('galeris')->insert([
            'user_id' => $adminId,
            'judul' => 'Kegiatan Upacara Senin',
            'deskripsi' => 'Dokumentasi upacara bendera.',
            'gambar' => 'default_galeri.jpg',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
