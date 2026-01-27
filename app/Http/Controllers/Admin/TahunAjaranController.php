<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Pastikan ini ada
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::latest()->get();
        $tingkatKelas = Kelas::select('tingkat')->distinct()->orderBy('tingkat', 'desc')->pluck('tingkat');

        return view('admin.tahun_ajaran.index', compact('tahunAjarans', 'tingkatKelas'));
    }

    public function store(Request $request)
    {
        // --- BAGIAN 1: FORMAT & VALIDASI (DARI KODE LAMA) ---
        // Tetap dipakai agar format tahun konsisten (YYYY/YYYY) dan berurutan
        $this->formatTahun($request);

        $request->validate([
            'tahun' => [
                'required', 'string', 'max:10', 'unique:tahun_ajarans,tahun',
                'regex:/^[0-9]{4}\/[0-9]{4}$/',
                function ($attribute, $value, $fail) {
                    $years = explode('/', $value);
                    if (count($years) == 2 && ((int)$years[1] - (int)$years[0]) !== 1) {
                        $fail('Tahun ajaran harus berurutan (Selisih 1 tahun).');
                    }
                },
            ],
        ], [
            'tahun.regex' => 'Format salah. Gunakan: 2024/2025 atau 2024-2025.',
            'tahun.unique' => 'Tahun ajaran ini sudah ada.',
        ]);

        // --- BAGIAN 2: LOGIKA PENYIMPANAN (DARI KODE BARU) ---
        // Menggunakan DB Transaction agar lebih aman
        try {
            DB::beginTransaction();

            // 1. Reset semua tahun ajaran menjadi tidak aktif
            // Menggunakan query() lebih bersih daripada where('is_active', true)
            TahunAjaran::query()->update(['is_active' => false]);

            // 2. Simpan tahun baru dan langsung set aktif
            TahunAjaran::create([
                'tahun' => $request->tahun,
                'is_active' => true,
            ]);

            DB::commit(); // Simpan permanen jika semua lancar

            return back()->with('success', 'Tahun Ajaran baru berhasil ditambah dan otomatis diaktifkan.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika ada error
            
            // Log error asli untuk developer
            Log::error("Gagal store TA: " . $e->getMessage());
            
            // Tampilkan pesan umum ke user (agar tidak membingungkan user dengan error code)
            return back()->with('error', 'Gagal menyimpan data sistem.');
        }
    }

    public function update(Request $request, $id)
    {
        $this->formatTahun($request); // Pastikan update juga dibersihkan formatnya

        $request->validate([
            'tahun' => [
                'required', 'string', 'max:10',
                Rule::unique('tahun_ajarans', 'tahun')->ignore($id),
                'regex:/^[0-9]{4}\/[0-9]{4}$/',
                function ($attribute, $value, $fail) {
                    $years = explode('/', $value);
                    if (count($years) == 2 && ((int)$years[1] - (int)$years[0]) !== 1) {
                        $fail('Tahun ajaran harus berurutan (Selisih 1 tahun).');
                    }
                },
            ],
        ]);

        try {
            $ta = TahunAjaran::findOrFail($id);
            $ta->update(['tahun' => $request->tahun]);
            return back()->with('success', 'Data tahun ajaran diperbarui.');
        } catch (\Exception $e) {
            Log::error("Gagal update TA ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function activate($id)
    {
        try {
            TahunAjaran::query()->update(['is_active' => false]);
            $ta = TahunAjaran::findOrFail($id);
            $ta->update(['is_active' => true]);
            return back()->with('success', "Tahun Ajaran {$ta->tahun} sekarang AKTIF.");
        } catch (\Exception $e) {
            Log::error("Gagal aktivasi TA: " . $e->getMessage());
            return back()->with('error', 'Gagal mengaktifkan tahun ajaran.');
        }
    }

    public function destroy($id)
{
        try {
            $ta = TahunAjaran::findOrFail($id);

            // 1. CEK STATUS AKTIF (Kodingan Lama - Dipertahankan)
            if ($ta->is_active) {
                return back()->with('error', 'Tidak bisa menghapus Tahun Ajaran yang sedang AKTIF.');
            }

            // 2. CEK RELASI SISWA (TAMBAHAN BARU - PENTING!)
            // Agar data siswa tidak kehilangan tahun ajarannya (menjadi NULL)
            $jumlahSiswa = \App\Models\Siswa::where('tahun_ajaran_id', $ta->id)->count();

            if ($jumlahSiswa > 0) {
                return back()->with('error', "Gagal Hapus! Masih ada $jumlahSiswa siswa yang terdaftar di tahun ajaran ini. Silakan pindahkan mereka atau luluskan dulu.");
            }

            // 3. EKSEKUSI HAPUS (Kodingan Lama - Dipertahankan)
            $ta->delete();
            
            return back()->with('success', 'Data berhasil dihapus.');

        } catch (\Exception $e) {
            // Error Handling (Kodingan Lama - Dipertahankan)
            \Illuminate\Support\Facades\Log::error("Gagal hapus Tahun Ajaran: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

        public function graduation(Request $request)
    {
        // 1. Validasi input
        $request->validate(['tingkat_akhir' => 'required']);

        try {
            // 2. (DARI KODE BARU) Menggunakan 'whereHas'
            // Mencari siswa 'Aktif' yang berada di dalam KELAS dengan tingkat sesuai input.
            // Ini lebih akurat daripada hanya mengecek kolom 'tingkat' di tabel siswa.
            $query = Siswa::where('status', 'Aktif')
                ->whereHas('kelas', function($q) use ($request) {
                    $q->where('tingkat', $request->tingkat_akhir);
                });

            $jumlah = $query->count();

            // 3. Cek jika kosong
            if ($jumlah == 0) {
                return back()->with('error', "Tidak ada siswa aktif di kelas {$request->tingkat_akhir}.");
            }

            // 4. (GABUNGAN) Lakukan Mass Update
            $query->update([
                'status' => 'Lulus',
                'kelas_id' => null,       // Melepas siswa dari kelas
                'tingkat' => $request->tingkat_akhir // (Opsional) Menyimpan jejak tingkat terakhir saat lulus
            ]);

            // 5. Pesan Sukses
            return back()->with('success', "BERHASIL! {$jumlah} siswa tingkat {$request->tingkat_akhir} telah diluluskan.");

        } catch (\Exception $e) {
            // 6. (DARI KODE LAMA) Error handling tetap menggunakan Log agar aman
            Log::error("Gagal proses kelulusan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat proses kelulusan.');
        }
    }


    // Helper Function agar tidak nulis str_replace berulang-ulang
    private function formatTahun(Request $request)
    {
        if ($request->has('tahun')) {
            $cleanTahun = str_replace('-', '/', $request->tahun);
            $request->merge(['tahun' => $cleanTahun]);
        }
    }
}