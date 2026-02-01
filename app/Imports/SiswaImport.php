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
use Maatwebsite\Excel\Concerns\Importable;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    use Importable, SkipsFailures;
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
            'nisn' => 'required|numeric|digits_between:8,12',            
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P,l,p',
            'kelas' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.numeric' => 'NISN harus berupa angka.',
            'nisn.digits_between' => 'NISN harus berjumlah 8-12 karakter.',
            'nama_lengkap.required' => 'Nama lengkap tidak boleh kosong.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'kelas.required' => 'Kolom Kelas wajib diisi (Contoh: 7A).',
        ];
    }

    public function model(array $row)
    {
        // 1. Bersihkan Data (Gunakan trim untuk menghindari spasi tak terlihat dari Excel)
        $nisn = trim($row['nisn']);
        $namaLengkap = trim($row['nama_lengkap']);

        // =========================================================================
        // [BARU DITAMBAHKAN] LOGIKA PENCEGAH (GUARD CLAUSE)
        // =========================================================================
        // Cek apakah siswa ini sudah ada di database berdasarkan NISN
        $siswaLama = Siswa::where('nisn', $nisn)->first();

        // Jika siswa ada DAN statusnya 'Lulus', 'Keluar', atau 'Pindah'
        if ($siswaLama && in_array($siswaLama->status, ['Lulus', 'Keluar', 'Pindah'])) {
            // STOP PROSES DISINI. 
            // Return null artinya baris Excel ini dilewati/diabaikan.
            // Data tidak akan di-update jadi 'Aktif', Akun User tidak akan disentuh.
            return null; 
        }
        // =========================================================================

        // LOGIKA EMAIL: namadepan.nisn@student.sekolah.id
        // explode memecah nama, strtolower mengecilkan huruf
        $namaDepan = strtolower(explode(' ', $namaLengkap)[0]);
        $emailFinal = $namaDepan . '.' . $nisn . '@student.sekolah.id';

        // 2. LOGIKA PEMECAH KELAS (Tetap Menggunakan Regex Anda)
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
            
                              // =========================================================================
            // [MODIFIKASI NOMOR 3]: Cek apakah data kelas ada di database
            // =========================================================================
            if (!$dataKelas) {
                // Jika Kelas Zonk (Tidak ada di DB), kembalikan null agar baris ini dilewati.
                // Ini mencegah pembuatan akun User tanpa kelas yang valid.
                return null; 
            }

            // Jika ada, baru ambil ID-nya
            $kelas_id = $dataKelas->id;
        } else {
            // Jika kolom kelas di Excel kosong sama sekali, lewati baris ini.
            return null;
        }                    
            // if ($dataKelas) {
            //     $kelas_id = $dataKelas->id;
            // }

        // 3. BUAT/UPDATE USER (AKUN LOGIN)
        $user = User::updateOrCreate(
            ['email' => $emailFinal], // Cari berdasarkan email hasil generate
            [
                'name' => $namaLengkap,
                'password' => Hash::make($nisn), // Rekomendasi: Password default = NISN
                'role' => 'siswa',
                'must_change_password' => true,
            ]
        );

        // 4. BUAT/UPDATE DATA SISWA
        return Siswa::updateOrCreate(
            ['nisn' => $nisn],
            [
                'user_id'         => $user->id,
                'nama_lengkap'    => $namaLengkap,
                'jenis_kelamin'   => strtoupper(trim($row['jenis_kelamin'])), 
                'kelas_id'        => $kelas_id,
                'tingkat'         => $tingkat, 
                'tahun_ajaran_id' => $this->tahunAktif ? $this->tahunAktif->id : null,
                'status'          => 'Aktif', // Ini hanya akan tereksekusi jika lolos dari pengecekan di atas
            ]
        );
    }
}