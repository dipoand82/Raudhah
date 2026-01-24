<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        // Tetap sama, sudah oke
        $kelas = Kelas::orderBy('tingkat', 'asc')
                      ->orderBy('nama_kelas', 'asc')
                      ->paginate(10);
        
        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat'    => 'required|integer|in:7,8,9', 
            'nama_kelas' => [
                'required',
                'string',
                'max:5',
                // 🔥 VALIDASI: Cek kombinasi Tingkat + Nama Kelas
                // Agar tidak ada dua kelas "7A"
                Rule::unique('kelas')->where(function ($query) use ($request) {
                    return $query->where('tingkat', $request->tingkat);
                }),
            ], 
        ], [
            // Custom pesan error
            'nama_kelas.unique' => "Gagal! Kelas {$request->tingkat}{$request->nama_kelas} sudah ada.",
        ]);

        Kelas::create([
            'tingkat' => $request->tingkat,
            'nama_kelas' => $request->nama_kelas,
        ]);

        return back()->with('success', 'Kelas berhasil dibuat!');
    }

    // Ubah $id agar konsisten dengan route web.php
    public function update(Request $request, $id) 
    {
        $request->validate([
            'tingkat'    => 'required|integer|in:7,8,9',
            'nama_kelas' => [
                'required',
                'string',
                'max:5',
                // Validasi unique update (abaikan diri sendiri)
                Rule::unique('kelas')->where(function ($query) use ($request) {
                    return $query->where('tingkat', $request->tingkat);
                })->ignore($id),
            ],
        ], [
            'nama_kelas.unique' => "Gagal! Kelas {$request->tingkat}{$request->nama_kelas} sudah digunakan.",
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update([
            'tingkat' => $request->tingkat,
            'nama_kelas' => $request->nama_kelas,
        ]);

        return back()->with('success', 'Data Kelas berhasil diperbarui!');
    }

    // 🔥 PERBAIKAN UTAMA ADA DI SINI (DESTROY) 🔥
    public function destroy($id)
    {
        // 1. Cari Kelas berdasarkan ID
        $kelas = Kelas::findOrFail($id);

        // 2. CEK KEAMANAN: Apakah kelas ini punya siswa?
        // Menggunakan relasi 'siswa' yang ada di Model Kelas
        if ($kelas->siswas()->count() > 0) {
            // Jika ada siswa, BATALKAN penghapusan dan beri pesan Error Merah
            return back()->with('error', 'Gagal Hapus! Kelas ini masih memiliki siswa aktif. Pindahkan siswa terlebih dahulu.');
        }

        // 3. Jika aman (kosong), baru dihapus
        $kelas->delete();
        
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}