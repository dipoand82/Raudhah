<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Imports\GuruImport;

class ManajemenUserController extends Controller
{
    // === 1. HALAMAN UTAMA (TABS) ===
    public function index(Request $request)
    {
        $search = $request->input('search');

        // A. Ambil Data User Role SISWA
        $userSiswa = User::where('role', 'siswa')
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('dataSiswa') 
            ->latest()
            ->paginate(10, ['*'], 'siswa_page');

        // B. Ambil Data User Role GURU
        $userGuru = User::where('role', 'guru')
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10, ['*'], 'guru_page');

        // C. Data Pendukung Modal (Kelas & Tahun)
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get(); 
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        return view('admin.manajemen_user.index', compact('userSiswa', 'userGuru', 'kelas', 'tahunAjaran'));
    }

    // === 2. SIMPAN SISWA (MANUAL LENGKAP) ===
    public function storeSiswa(Request $request)
    {
        // Validasi Input (Termasuk Kelas & Gender)
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'nisn' => 'nullable|unique:siswas,nisn',
            'kelas_id' => 'nullable|exists:kelas,id', // Validasi ID Kelas
            'jenis_kelamin' => 'required|in:L,P',     // Validasi L/P
        ]);

        // A. Buat Akun Login
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'siswa',
            'password' => Hash::make('12345678'),
            'must_change_password' => true,
        ]);

        // B. Buat Data Siswa (Langsung simpan Kelas & Gender)
        Siswa::create([
            'user_id' => $user->id,
            'nisn' => $request->nisn,
            'nama_lengkap' => $request->name, // Ambil dari inputan nama user
            'kelas_id' => $request->kelas_id,           // <--- Simpan Kelas
            'jenis_kelamin' => $request->jenis_kelamin, // <--- Simpan Gender
            'status' => 'Aktif',
            // Otomatis set tahun masuk ke tahun ajaran yang sedang aktif
            'tahun_masuk_id' => TahunAjaran::where('is_active', true)->value('id'), 
        ]);

        return back()->with('success', 'Akun Siswa berhasil dibuat & data tersimpan!');
    }

    // === 3. IMPORT SISWA (DENGAN PENGAMAN) ===
    public function importSiswa(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        
        try {
            Excel::import(new SiswaImport, $request->file('file'));
            return back()->with('success', 'Import Siswa Berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    // === 4. SIMPAN GURU ===
    public function storeGuru(Request $request)
    {
        $request->validate(['name' => 'required', 'email' => 'required|email|unique:users']);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'guru',
            'password' => Hash::make('12345678'),
            'must_change_password' => true,
        ]);

        return back()->with('success', 'Akun Guru berhasil dibuat!');
    }

    // === 5. IMPORT GURU ===
    public function importGuru(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        
        try {
            Excel::import(new GuruImport, $request->file('file'));
            return back()->with('success', 'Import Guru Berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    // === 6. UBAH PASSWORD ADMIN ===
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password Admin berhasil diubah!');
    }
}