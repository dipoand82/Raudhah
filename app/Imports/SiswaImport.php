<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;
    use SkipsFailures;

    protected $tahunAktif;

    public $fallbackClasses = [];

    public function __construct()
    {
        set_time_limit(300);
        $this->tahunAktif = TahunAjaran::where('is_active', true)->first();
    }

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
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap tidak boleh kosong.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'kelas.required' => 'Kolom Kelas wajib diisi (Contoh: 7A).',
        ];
    }

    public function model(array $row)
    {
        $nisn = trim($row['nisn'] ?? '');
        $namaLengkap = trim($row['nama_lengkap'] ?? '');
        $siswaLama = Siswa::where('nisn', $nisn)->first();
        if ($siswaLama && in_array($siswaLama->status, ['Lulus', 'Keluar', 'Pindah'])) {
            return null;
        }
        $namaDepan = strtolower(explode(' ', $namaLengkap)[0]);
        $emailFinal = $namaDepan.'.'.$nisn.'@raudhah.com';
        $kelas_id = $siswaLama ? $siswaLama->kelas_id : null;
        $tingkat = $siswaLama ? $siswaLama->tingkat : 7;

        if (! empty($row['kelas'])) {
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
            } elseif ($siswaLama) {
                $this->fallbackClasses[] = [
                    'nama' => $namaLengkap,
                    'input' => $row['kelas'],
                    'tetap' => ($siswaLama->kelas)
                                ? $siswaLama->kelas->tingkat.' '.$siswaLama->kelas->nama_kelas
                                : 'Belum Ada Kelas',
                ];
            }
        }

        try {
            $user = User::where('email', $emailFinal)->first();

            if ($user) {
                $user->update([
                    'name' => $namaLengkap,
                ]);
            } else {
                $user = User::create([
                    'name' => $namaLengkap,
                    'email' => $emailFinal,
                    'password' => Hash::make($nisn),
                    'role' => 'siswa',
                    'must_change_password' => true,
                ]);
            }

            return Siswa::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'user_id' => $user->id,
                    'nama_lengkap' => $namaLengkap,
                    'jenis_kelamin' => strtoupper(trim($row['jenis_kelamin'] ?? '')),
                    'kelas_id' => $kelas_id,
                    'tingkat' => $tingkat,
                    'tahun_ajaran_id' => $this->tahunAktif->id ?? null,
                    'status' => 'Aktif',
                ]
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}
