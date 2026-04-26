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
        $galeri = Galeri::latest()->paginate(6);
        return view('galeri.index', compact('profil_sekolah', 'galeri'));
    }

public function show($id)
{
    $profil_sekolah = \App\Models\ProfilSekolah::first();
    $item = \App\Models\Galeri::findOrFail($id);
    return view('galeri.show', compact('profil_sekolah', 'item'));
}
}
