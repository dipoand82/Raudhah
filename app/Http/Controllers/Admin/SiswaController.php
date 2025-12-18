<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    // 1. Tampilkan HANYA Siswa
    public function index()
    {
        $siswas = User::where('role', 'siswa')->latest()->paginate(10);
        return view('admin.siswas.index', compact('siswas'));
    }

    // 2. Form Tambah Siswa (Manual)
    public function create()
    {
        return view('admin.siswas.create');
    }

    // 3. Simpan Siswa (Manual)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'siswa', // Otomatis Role Siswa
            'password' => Hash::make('12345678'),
            'must_change_password' => true,
        ]);

        return redirect()->route('admin.siswas.index')->with('success', 'Akun Siswa berhasil dibuat!');
    }

    // 4. Import Siswa (Massal)
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        
        Excel::import(new SiswaImport, $request->file('file'));

        return redirect()->route('admin.siswas.index')->with('success', 'Data Siswa massal berhasil diimport!');
    }
}