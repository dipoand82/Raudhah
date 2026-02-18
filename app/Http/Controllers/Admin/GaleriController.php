<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan ini ada agar tidak error saat hapus file
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    // 1. Menampilkan daftar galeri
    public function index()
    {
        $galeri = Galeri::latest()->get();

        return view('admin.galeri.index', compact('galeri'));
    }

    // 2. Form Tambah Foto
    public function create()
    {
        return view('admin.galeri.create');
    }

    // 3. Proses Simpan Foto Baru
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'judul' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ],[
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'gambar.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('galeri', 'public');

            Galeri::create([
                'user_id' => Auth::id(),
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'gambar' => $path,
            ]);
        }

        // Ganti baris return lama Anda menjadi:
        return redirect()->route('admin.profil.edit', ['tab' => 'galeri'])
            ->with('success', 'Galeri berhasil ditambahkan!');
    }

    // 4. Form Edit Foto (Yang Anda kirim tadi)
    public function edit($id)
    {
        $galeri = Galeri::findOrFail($id);

        return view('admin.galeri.edit', compact('galeri', ));
    }

    // 5. Proses Update Foto
    public function update(Request $request, $id)
    {
        $galeri = Galeri::findOrFail($id);

        $request->validate([
            'judul' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ],[
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'gambar.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        ]);

        $data = $request->only(['judul', 'deskripsi']);

        if ($request->hasFile('gambar')) {
            // Hapus foto lama
            if ($galeri->gambar) {
                Storage::disk('public')->delete($galeri->gambar);
            }
            // Simpan foto baru
            $data['gambar'] = $request->file('gambar')->store('galeri', 'public');
        }

        $galeri->update($data);
        // Cek input hidden 'current_tab' yang kita buat tadi
        $targetTab = $request->input('current_tab', 'profil'); // default ke profil jika tidak ada

        // Ganti baris return lama Anda menjadi seperti ini:
        return redirect()->route('admin.profil.edit', ['tab' => 'galeri'])
            ->with('success', 'Foto galeri berhasil diperbarui!');
    }

    // 6. Proses Hapus Foto
    public function destroy($id)
    {
        // Mengambil data galeri berdasarkan ID
        $galeri = Galeri::findOrFail($id);

        // Cek jika ada file gambar, maka hapus dari storage public
        if ($galeri->gambar && Storage::disk('public')->exists($galeri->gambar)) {
            Storage::disk('public')->delete($galeri->gambar);
        }

        // Hapus data dari database
        $galeri->delete();

        // Kembali ke halaman edit profil dengan pesan sukses
        // Ganti baris return lama Anda menjadi:
        return redirect()->route('admin.profil.edit', ['tab' => 'galeri'])
            ->with('success', 'Foto berhasil dihapus dari galeri SMP IT Raudhah!');
    }
}
