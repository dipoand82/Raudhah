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

// Tambahkan baris ini untuk mengetes
    return view('admin.tahun_ajaran.index', compact('tahunAjarans', 'tingkatKelas'))
           ->with('error', 'Ini adalah tes pesan error untuk memastikan Alert muncul');    }

    public function store(Request $request)
    {// TARUH DI SINI UNTUK TES TOMBOL SIMPAN

    // Kode di bawah ini tidak akan dieksekusi saat ngetes
    $this->formatTahun($request);
        // ---  BAGIAN 1: FORMAT & VALIDASI (DARI KODE LAMA) ---
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
    DB::rollBack();
    Log::error("Gagal Menyimpan Tahun Ajaran: " . $e->getMessage());

    // Pesan ini yang akan dilihat user jika database error
    return back()->with('error', 'Gagal menyimpan data ke sistem. Silakan coba lagi nanti.');
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
                return back()->with('error', "Gagal Hapus! Masih ada $jumlahSiswa siswa yang terdaftar di tahun ajaran ini");
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
    $request->validate(['tingkat_akhir' => 'required']);

    try {
        // Cari siswa aktif berdasarkan tingkat kelas
        $query = Siswa::whereIn('status', ['Aktif', 'aktif'])
            ->whereHas('kelas', function($q) use ($request) {
                $q->where('tingkat', $request->tingkat_akhir);
            });

        $jumlah = $query->count();

        // JIKA TIDAK ADA DATA, KIRIM ERROR DAN STOP PROSES
        if ($jumlah === 0) {
            return back()->with('error', "Gagal! Tidak ada siswa aktif yang ditemukan di kelas tingkat {$request->tingkat_akhir}.");
        }

        // Jika ada data, lanjutkan update
        DB::beginTransaction();
        $ids = $query->pluck('id');

        Siswa::whereIn('id', $ids)->update([
            'status' => 'Lulus',
            'kelas_id' => null,
        ]);

        DB::commit();
        return back()->with('success', "BERHASIL! {$jumlah} siswa telah diluluskan.");

    } catch (\Exception $e) {
        if (DB::transactionLevel() > 0) DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
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
