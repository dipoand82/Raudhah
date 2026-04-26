<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\ProfilSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilSekolahController extends Controller
{
    public function edit()
    {
        $profil = ProfilSekolah::firstOrCreate(
            ['id' => 1],
            ['nama_sekolah' => 'SMP IT Raudhah']
        );
        $galeri = Galeri::latest()->paginate(6)->withQueryString();

        return view('admin.profil.edit', data: compact('profil', 'galeri'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'sometimes|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'brosur_info' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'program_unggulan' => 'nullable|string',
            'alasan_memilih' => 'nullable|string',
            'deskripsi_singkat' => 'nullable|string',
            'info_penting' => 'nullable|string',
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
                'logo.image' => 'File logo harus berupa gambar.',
                'logo.mimes' => 'Format logo harus jpeg, png, atau jpg.',
                'logo.max' => 'Ukuran logo tidak boleh lebih dari 2 MB.',
                'banner.image' => 'File banner harus berupa gambar.',
                'banner.mimes' => 'Format banner harus jpeg, png, atau jpg.',
                'banner.max' => 'Ukuran banner tidak boleh lebih dari 4 MB.',
                'brosur_info.image' => 'File brosur harus berupa gambar.',
                'brosur_info.mimes' => 'Format brosur harus jpeg, png, atau jpg.',
                'brosur_info.max' => 'Ukuran file brosur tidak boleh lebih dari 4 MB.',
                'nama_sekolah.max' => 'Nama sekolah terlalu panjang (maksimal 255 karakter).',
                'email.email' => 'Alamat email yang dimasukkan tidak valid.',
                'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            ]);

        $profil = ProfilSekolah::first();

        $data = $request->except(['logo', 'banner', 'brosur_info']);

        if ($request->hasFile('logo')) {
            if ($profil->logo_path && Storage::exists('public/'.$profil->logo_path)) {
                Storage::delete('public/'.$profil->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($profil->banner_path && Storage::disk('public')->exists($profil->banner_path)) {
                Storage::disk('public')->delete($profil->banner_path);
            }
            $data['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        if ($request->hasFile('brosur_info')) {
            if ($profil->brosur_info && Storage::disk('public')->exists($profil->brosur_info)) {
                Storage::disk('public')->delete($profil->brosur_info);
            }
            $data['brosur_info'] = $request->file('brosur_info')->store('brosur_info', 'public');
        }

        $profil->update($data);
        $targetTab = $request->input('current_tab', 'profil');

        return redirect()->route('admin.profil.edit', ['tab' => $targetTab])
            ->with('success', 'Profil Sekolah berhasil diperbarui!');
    }
}
