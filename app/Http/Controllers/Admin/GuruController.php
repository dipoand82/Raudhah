<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Imports\GuruImport;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    /**
     * 1. Tampilkan Daftar Guru dengan fitur Pencarian
     */
public function index(Request $request)
{
    // 1. Tangkap keyword dari input name="search_guru"
    $search = $request->input('search_guru');

    // 2. Query Guru
    $userGuru = User::where('role', 'guru')
        ->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(30)
        ->withQueryString();

    // 3. Ambil data pendukung lainnya agar view tidak error
    $userSiswa = User::where('role', 'siswa')->paginate(30);
    $kelas = \App\Models\Kelas::all();

    return view('admin.manajemen-user.index', compact('userGuru', 'userSiswa', 'kelas'));
}

    /**
     * 2. Simpan Guru Manual
     */
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:users',
    ]);

    $email = $request->email;

    // Logika Otomatis jika email kosong
    if (!$email) {
        // Hapus spasi dan simbol dari seluruh nama
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name));
        $email = $username . '@raudhah.com';

        // Cek jika email sudah terpakai (karena nama mirip)
        $count = \App\Models\User::where('email', 'like', $username . '%')->count();
        if ($count > 0) {
            $email = $username . ($count + 1) . '@raudhah.com';
        }
    }

    \App\Models\User::create([
        'name' => $request->name,
        'email' => $email,
        'role' => 'guru',
        'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
        'must_change_password' => false, // Set false agar bisa langsung login cek laporan
    ]);

    return redirect()->route('admin.manajemen-user.index', ['tab' => 'guru'])
                     ->with('success', "Akun Guru {$request->name} berhasil dibuat! Email: {$email}");
}

    /**
     * 3. Update Data Guru
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email'));

        return redirect()->route('admin.manajemen-user.index', ['tab' => 'guru'])
                         ->with('success', 'Data Guru berhasil diperbarui.');
    }

    /**
     * 4. Import Guru Massal
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ], [
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv'
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file'));

            return redirect()->route('admin.manajemen-user.index', ['tab' => 'guru'])
                             ->with('success', 'Data Guru berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.manajemen-user.index', ['tab' => 'guru'])
                             ->with('error', 'Gagal mengimport data. Periksa kembali format file Anda.');
        }
    }

    /**
     * 5. Download Template Excel
     */
public function downloadTemplate()
{
    // Memanggil Class Export yang sudah ada kolom 'No'-nya
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\TemplateGuruExport,
        'template_import_guru_raudhah.xlsx'
    );
}
}
