<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\ProfilSekolah; // <--- 1. JANGAN LUPA PANGGIL INI
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    // 1. TAMPILKAN HALAMAN (KIRI: TABEL, KANAN: KELULUSAN)
    public function index()
    {
        $tahunAjarans = TahunAjaran::latest()->get();

        // Ambil data sekolah 
        $profil_sekolah = ProfilSekolah::first();

        return view('admin.tahun_ajaran.index', compact('tahunAjarans', 'profil_sekolah'));

    }

    // 2. SIMPAN TAHUN AJARAN BARU
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string', // Contoh: 2024/2025
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        // Kalau yang baru diset Aktif, yang lama jadi Tidak Aktif
        if ($request->has('is_active')) {
            TahunAjaran::query()->update(['is_active' => false]);
        }

        TahunAjaran::create([
            'tahun' => $request->tahun,
            'semester' => $request->semester,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Tahun Ajaran berhasil dibuat!');
    }

    // 3. HAPUS TAHUN AJARAN
    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return back()->with('success', 'Data dihapus!');
    }

    // 4. SET AKTIF (Tombol Switch Cepat)
    public function activate($id)
    {
        // Matikan semua
        TahunAjaran::query()->update(['is_active' => false]);
        
        // Aktifkan yang dipilih
        TahunAjaran::where('id', $id)->update(['is_active' => true]);

        return back()->with('success', 'Tahun Ajaran aktif berhasil diganti!');
    }

    // 5. PROSES KELULUSAN MASSAL
    public function processGraduation(Request $request)
    {
        // Ubah semua siswa yang statusnya 'Aktif' menjadi 'Lulus'
        $jumlah = Siswa::where('status', 'Aktif')->update(['status' => 'Lulus']);

        if ($jumlah > 0) {
            return back()->with('success', "Alhamdulillah! {$jumlah} siswa telah dinyatakan LULUS.");
        }

        return back()->with('error', 'Tidak ada siswa aktif untuk diluluskan.');
    }
}