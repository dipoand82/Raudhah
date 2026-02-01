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
    public $fallbackClasses = [];

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
            // 'nisn.unique' => 'NISN sudah terdaftar.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.', 
            'nama_lengkap.required' => 'Nama lengkap tidak boleh kosong.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'kelas.required' => 'Kolom Kelas wajib diisi (Contoh: 7A).',
        ];
    }

    public function model(array $row)
    {
        // 1. Bersihkan Data
        $nisn = trim($row['nisn'] ?? '');
        $namaLengkap = trim($row['nama_lengkap'] ?? '');

        // Cek siswa lama untuk fallback
        $siswaLama = Siswa::where('nisn', $nisn)->first();

        // Logika Guard: Jika sudah Lulus/Pindah, abaikan (Silent Skip)
        if ($siswaLama && in_array($siswaLama->status, ['Lulus', 'Keluar', 'Pindah'])) {
            return null; 
        }

        // 2. Logika Email
        $namaDepan = strtolower(explode(' ', $namaLengkap)[0]);
        $emailFinal = $namaDepan . '.' . $nisn . '@raudhah.com';

        // 3. Logika Fallback Kelas
        $kelas_id = $siswaLama ? $siswaLama->kelas_id : null;
        $tingkat = $siswaLama ? $siswaLama->tingkat : 7;

        if (!empty($row['kelas'])) {
            $kelasRaw = strtoupper(str_replace(' ', '', $row['kelas'])); 
            preg_match('/^\d+/', $kelasRaw, $matchesTingkat);
            $currTingkat = $matchesTingkat[0] ?? null;
            $namaKelas = str_replace($currTingkat, '', $kelasRaw); 

            $dataKelas = Kelas::where('tingkat', $currTingkat)
                            ->where('nama_kelas', $namaKelas)
                            ->first();
            
            if ($dataKelas) {
                $kelas_id = $dataKelas->id;
                $tingkat = $dataKelas->tingkat;
            } else if ($siswaLama) {
                // JIKA KELAS ZONK: Catat info biru, tapi jangan hentikan proses
                $this->fallbackClasses[] = [
                    'nama' => $namaLengkap,
                    'input' => $row['kelas'],
                    'tetap' => ($siswaLama->kelas) 
                                ? $siswaLama->kelas->tingkat . ' ' . $siswaLama->kelas->nama_kelas 
                                : 'Belum Ada Kelas'
                ];
            }
        }

        // 4. Buat/Update User
        // Kita gunakan try-catch kecil agar jika NISN/Nama kosong tidak bikin crash sebelum divalidasi rules()
        try {
            $user = User::updateOrCreate(
                ['email' => $emailFinal],
                [
                    'name' => $namaLengkap,
                    'password' => Hash::make($nisn),
                    'role' => 'siswa',
                    'must_change_password' => true,
                ]
            );

            return Siswa::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'user_id'         => $user->id,
                    'nama_lengkap'    => $namaLengkap,
                    'jenis_kelamin'   => strtoupper(trim($row['jenis_kelamin'] ?? '')), 
                    'kelas_id'        => $kelas_id,
                    'tingkat'         => $tingkat, 
                    'tahun_ajaran_id' => $this->tahunAktif->id ?? null,
                    'status'          => 'Aktif',
                ]
            );
        } catch (\Exception $e) {
            // Biarkan Laravel Excel yang menangani error lewat rules()
            return null;
        }
    }
}