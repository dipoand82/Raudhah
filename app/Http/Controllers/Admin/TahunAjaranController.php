<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    // 1. TAMPILKAN DATA
    public function index()
    {
        $tahunAjarans = TahunAjaran::latest()->get();
        
        // Ambil daftar tingkat kelas (misal: 7, 8, 9) untuk dropdown kelulusan
        $tingkatKelas = Kelas::select('tingkat')->distinct()->orderBy('tingkat', 'desc')->pluck('tingkat');

        return view('admin.tahun_ajaran.index', compact('tahunAjarans', 'tingkatKelas'));
    }

    // 2. SIMPAN BARU
    public function store(Request $request)
    {
        // PERBAIKAN: Hapus validasi semester
        $request->validate([
            'tahun' => 'required|unique:tahun_ajarans,tahun', // Tambah unique biar gak dobel
        ]);

        // Jika user mencentang "Langsung Aktifkan"
        if ($request->has('is_active')) {
            // Matikan semua tahun lain dulu
            TahunAjaran::query()->update(['is_active' => false]);
        }

        TahunAjaran::create([
            'tahun' => $request->tahun,
            // PERBAIKAN: Hapus baris 'semester' => ...
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Tahun ajaran baru berhasil ditambahkan.');
    }

    // 3. SET AKTIF (Ganti Tahun Berjalan)
    public function activate($id)
    {
        // 1. Matikan semua
        TahunAjaran::query()->update(['is_active' => false]);
        
        // 2. Aktifkan yang dipilih
        $ta = TahunAjaran::findOrFail($id);
        $ta->update(['is_active' => true]);

        // PERBAIKAN: Hapus {$ta->semester} dari pesan sukses
        return back()->with('success', "Tahun Ajaran {$ta->tahun} sekarang AKTIF.");
    }

    // 4. HAPUS
    public function destroy($id)
    {
        $ta = TahunAjaran::findOrFail($id);
        if ($ta->is_active) {
            return back()->with('error', 'Tidak bisa menghapus Tahun Ajaran yang sedang AKTIF.');
        }
        $ta->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    // 5. UPDATE (EDIT)
    public function update(Request $request, $id)
    {
        $ta = TahunAjaran::findOrFail($id);
        
        // PERBAIKAN: Validasi unique kecuali punya sendiri
        $request->validate([
            'tahun' => 'required|unique:tahun_ajarans,tahun,'.$id,
        ]);

        $ta->update([
            'tahun' => $request->tahun,
            // PERBAIKAN: Hapus baris 'semester' => ...
        ]);

        return back()->with('success', 'Data tahun ajaran diperbarui.');
    }

    // === FITUR SPESIAL: PROSES KELULUSAN (Opsional / Jarang Dipakai jika pakai Import Excel) ===
    public function graduation(Request $request)
    {
        $request->validate([
            'tingkat_akhir' => 'required', // Admin harus pilih, misal: Tingkat 9
        ]);

        // Logika: Cari Siswa yang Statusnya 'Aktif' DAN berada di Kelas Tingkat 9
        // PERBAIKAN: Pastikan kolom 'tingkat' sudah ada di tabel siswa (sesuai migration baru)
        $siswaLulus = Siswa::where('status', 'Aktif')
            ->where('tingkat', $request->tingkat_akhir) // Langsung cek kolom tingkat di tabel siswa
            ->get();

        $jumlah = $siswaLulus->count();

        if ($jumlah == 0) {
            return back()->with('error', 'Tidak ada siswa aktif ditemukan pada tingkat kelas tersebut.');
        }

        // Eksekusi Update Status Massal
        foreach ($siswaLulus as $siswa) {
            $siswa->update([
                'status' => 'Lulus',
                'kelas_id' => null, // Cabut kelasnya agar tidak punya kelas lagi
            ]); 
        }

        return back()->with('success', "BERHASIL! Sebanyak {$jumlah} siswa tingkat {$request->tingkat_akhir} telah diluluskan.");
    }
}