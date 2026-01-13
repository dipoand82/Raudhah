<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Imports\GuruImport; // Import file yang baru dibuat
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    // 1. Tampilkan HANYA Guru
    public function index()
    {
        $gurus = User::where('role', 'guru')->latest()->paginate(10);
        return view('admin.gurus.index', compact('gurus'));
    }

    // 2. Form Tambah Guru (Manual)
    public function create()
    {
        return view('admin.gurus.create');
    }

    // 3. Simpan Guru (Manual)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'guru', // Otomatis Guru
            'password' => Hash::make('12345678'),
            'must_change_password' => true,
        ]);

        return redirect()->route('admin.gurus.index')->with('success', 'Data Guru berhasil ditambahkan!');
    }

    // 4. Import Guru (Massal)
    // public function import(Request $request)
    // {
    //     $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        
    //     Excel::import(new GuruImport, $request->file('file'));

    //     return redirect()->route('admin.gurus.index')->with('success', 'Data Guru berhasil diimport!');
    // }
    public function import(Request $request)
{
     $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        
    Excel::import(new GuruImport, $request->file('file'));

    return redirect()->route('admin.gurus.index')
        ->with('success', 'Data Guru berhasil diimport!');
}

}