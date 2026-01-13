<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;

class SiswaController extends Controller
{
    // === 1. HALAMAN BUKU INDUK SISWA ===
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Note: Pastikan relasi di Model Siswa bernama 'tahunMasuk' (bukan tahun_masuk)
        $siswas = Siswa::with(['user', 'kelas', 'tahunMasuk'])
            ->when($search, function($query) use ($search) {
                $query->where('nisn', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(10);

        return view('admin.data_siswa.index', compact('siswas'));
    }

    // === 2. HALAMAN EDIT DETAIL ===
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Data pendukung dropdown
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();
        
        return view('admin.data_siswa.edit', compact('siswa', 'kelas', 'tahunAjaran'));
    }

    // === 3. PROSES UPDATE ===
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Update User (Nama & Email)
        $siswa->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update Data Siswa
        $siswa->update([
            'nisn' => $request->nisn,
            'kelas_id' => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tahun_masuk_id' => $request->tahun_masuk_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.siswas.index')->with('success', 'Data siswa diperbarui!');
    }

    // === 4. HAPUS SISWA ===
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->user->delete(); // Otomatis hapus siswa juga (Cascade)
        return back()->with('success', 'Data siswa dihapus.');
    }
}