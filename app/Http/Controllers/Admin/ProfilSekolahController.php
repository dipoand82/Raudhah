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
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi Logo
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:4096', // Validasi Banner (Max 4MB)
        ]);

        $profil = ProfilSekolah::first();

        // Data yang mau diupdate
        $data = $request->except(['logo', 'banner']); // Kecualikan file dulu

        // Logic Upload Logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($profil->logo_path && Storage::exists('public/' . $profil->logo_path)) {
                Storage::delete('public/' . $profil->logo_path);
            }
            
            // Simpan logo baru
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $path;
        }
    
        //Logic Upload Banner (BARU)
        if ($request->hasFile('banner')) {
            // Hapus banner lama
            if ($profil->banner_path && Storage::exists('public/' . $profil->banner_path)) {
                Storage::delete('public/' . $profil->banner_path);
            }
            // Simpan banner baru
            $data['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        $profil->update($data);

        return redirect()->back()->with('success', 'Profil Sekolah berhasil diperbarui!');
    }
}