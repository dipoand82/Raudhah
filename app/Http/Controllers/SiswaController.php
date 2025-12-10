<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        // Mengambil semua data siswa dari database
        $semuaSiswa = Siswa::all();

        // Tampilkan halaman view dan kirim data siswa ke sana
        return view('siswa.index', [
            'siswas' => $semuaSiswa
        ]);
    }
}
