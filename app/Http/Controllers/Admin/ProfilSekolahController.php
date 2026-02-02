<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilSekolahController extends Controller
{
    public function edit()
    {
        // Ambil data pertama, atau buat baru kosong jika belum ada
        $profil = ProfilSekolah::firstOrCreate(
            ['id' => 1],
            ['nama_sekolah' => 'SMP IT Raudhah']
        );

        return view('admin.profil.edit', compact('profil'));
    }

    public function update(Request $request)
    {
        // 1. Tambahkan field baru ke dalam validasi
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'program_unggulan' => 'nullable|string', // Validasi baru
            'alasan_memilih' => 'nullable|string',   // Validasi baru
            'deskripsi_singkat' => 'nullable|string',   // Validasi baru

        ]);

        $profil = ProfilSekolah::first();

        // 2. Ambil semua input kecuali file
        $data = $request->except(['logo', 'banner']);

        // Logic Upload Logo
        if ($request->hasFile('logo')) {
            if ($profil->logo_path && Storage::exists('public/' . $profil->logo_path)) {
                Storage::delete('public/' . $profil->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        // Logic Upload Banner
        if ($request->hasFile('banner')) {
            if ($profil->banner_path && Storage::exists('public/' . $profil->banner_path)) {
                Storage::delete('public/' . $profil->banner_path);
            }
            $data['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        // 3. Update database (Data teks + path gambar akan terupdate di sini)
        $profil->update($data);

        return redirect()->back()->with('success', 'Profil Sekolah berhasil diperbarui!');
    }
}
