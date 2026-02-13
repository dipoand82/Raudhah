<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use App\Models\ProfilSekolah;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index()
    {
        $profil_sekolah = ProfilSekolah::first();
        // Menampilkan semua foto dengan scroll/halaman (seperti Gambar 2)
        $galeri = Galeri::latest()->paginate(12);
        return view('galeri.index', compact('profil_sekolah', 'galeri'));
    }

public function show($id)
{
    $profil_sekolah = \App\Models\ProfilSekolah::first();
    $item = \App\Models\Galeri::findOrFail($id);

    // Pastikan memanggil 'galeri.show' (folder galeri, file show.blade.php)
    return view('galeri.show', compact('profil_sekolah', 'item'));
}
}
