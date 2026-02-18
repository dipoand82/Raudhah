<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate; // Pastikan ini ada untuk otorisasi akses admin
use Illuminate\Support\Facades\Log; // Pastikan ini ada untuk logging error jika diperlukan
use App\Models\Galeri; // Pastikan ini ada untuk mengambil data galeri di tab profil

class ProfilSekolahController extends Controller
{
    public function edit()
    {
        // Ambil data pertama, atau buat baru kosong jika belum ada
        $profil = ProfilSekolah::firstOrCreate(
            ['id' => 1],
            ['nama_sekolah' => 'SMP IT Raudhah']
        );

        // Tambahkan baris ini agar data galeri bisa tampil di tab
        // $galeri = \App\Models\Galeri::latest()->get();
        $galeri = Galeri::latest()->paginate(6)->withQueryString();

        return view('admin.profil.edit', data: compact('profil', 'galeri'));
    }

    public function update(Request $request)
    {
        // 1. Tambahkan field baru ke dalam validasi
        $request->validate([
            'nama_sekolah' => 'sometimes|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'brosur_info' => 'nullable|image|mimes:jpeg,png,jpg|max:4096', // Validasi baru
            'program_unggulan' => 'nullable|string', // Validasi baru
            'alasan_memilih' => 'nullable|string',   // Validasi baru
            'deskripsi_singkat' => 'nullable|string',   // Validasi baru
            'info_penting' => 'nullable|string',   // Validasi baru
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'info_footer' => 'nullable|string',

        ],
        [
// Custom Messages untuk Logo
    'logo.image' => 'File logo harus berupa gambar.',
    'logo.mimes' => 'Format logo harus jpeg, png, atau jpg.',
    'logo.max' => 'Ukuran logo tidak boleh lebih dari 2 MB.',

    // Custom Messages untuk Banner
    'banner.image' => 'File banner harus berupa gambar.',
    'banner.mimes' => 'Format banner harus jpeg, png, atau jpg.',
    'banner.max' => 'Ukuran banner tidak boleh lebih dari 4 MB.',

    // Custom Messages untuk Brosur Info
    'brosur_info.image' => 'File brosur harus berupa gambar.',
    'brosur_info.mimes' => 'Format brosur harus jpeg, png, atau jpg.',
    'brosur_info.max' => 'Ukuran file brosur tidak boleh lebih dari 4 MB.',

    // Custom Messages untuk Field Teks
    'nama_sekolah.max' => 'Nama sekolah terlalu panjang (maksimal 255 karakter).',
    'email.email' => 'Alamat email yang dimasukkan tidak valid.',
    'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
        ]);

        $profil = ProfilSekolah::first();

        // 2. Ambil semua input kecuali file
        $data = $request->except(['logo', 'banner', 'brosur_info']);

        // Logic Upload Logo
        if ($request->hasFile('logo')) {
            if ($profil->logo_path && Storage::exists('public/'.$profil->logo_path)) {
                Storage::delete('public/'.$profil->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        // Logic Upload Banner
        if ($request->hasFile('banner')) {
            // Hapus file lama
            if ($profil->banner_path && Storage::disk('public')->exists($profil->banner_path)) {
                Storage::disk('public')->delete($profil->banner_path);
            }
            // Simpan yang baru
            $data['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        // Logic Upload brosur info
        if ($request->hasFile('brosur_info')) {
            // Hapus file lama
            if ($profil->brosur_info && Storage::disk('public')->exists($profil->brosur_info)) {
                Storage::disk('public')->delete($profil->brosur_info);
            }
            // Simpan yang baru
            $data['brosur_info'] = $request->file('brosur_info')->store('brosur_info', 'public');
        }

        // 3. Update database (Data teks + path gambar akan terupdate di sini)
        $profil->update($data);
// Ambil nilai penanda tab dari form, jika kosong default ke 'profil'
$targetTab = $request->input('current_tab', 'profil');
        // return redirect()->back()->with('success', 'Profil Sekolah berhasil diperbarui!');
        return redirect()->route('admin.profil.edit', ['tab' => $targetTab])
                     ->with('success', 'Profil Sekolah berhasil diperbarui!');
    }
}
