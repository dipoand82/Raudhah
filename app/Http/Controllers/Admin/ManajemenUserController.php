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
use App\Exports\TemplateSiswaExport;
use App\Imports\SiswaImport;
use App\Imports\GuruImport;
use Illuminate\Support\Str; // <--- WAJIB TAMBAHKAN INI (Baris Penting)

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
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('dataSiswa', fn($s) => $s->where('nisn', 'like', "%{$search}%"));
            })
            ->with('dataSiswa.kelas') 
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
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(); 
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        // Pastikan nama folder view sesuai (biasanya admin.manajemen-user.index)
        return view('admin.manajemen_user.index', compact('userSiswa', 'userGuru', 'kelas', 'tahunAjaran'));
    }

    // === 2. SIMPAN SISWA (MANUAL LENGKAP) ===
    public function storeSiswa(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:users',
            'nisn' => 'required|unique:siswas,nisn',
            'kelas_id' => 'nullable|exists:kelas,id', 
            'jenis_kelamin' => 'required|in:L,P',     
        ]);

        // 2. Ambil Data Kelas & Tahun
        $kelas = Kelas::find($request->kelas_id);
        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        // 3. LOGIKA EMAIL: NamaDepan.NISN@student...
        if ($request->filled('email')) {
            $email = $request->email;
        } else {
            // Ambil nama depan saja (Contoh: "Budi Santoso" -> "Budi")
            $namaDepan = explode(' ', $request->name)[0]; 
            // Bersihkan (huruf kecil, hapus simbol aneh)
            $namaBersih = Str::slug($namaDepan);
            // Gabung
            $email = $namaBersih . '.' . $request->nisn . '@student.sekolah.id';
        }

        // 4. BUAT USER (Password = NISN)
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'role' => 'siswa',
            'password' => Hash::make($request->nisn), // <--- Password Awal = NISN
            'must_change_password' => true, // Wajib ganti password saat login pertama
        ]);

        // 5. Simpan Detail Siswa
        Siswa::create([
            'user_id' => $user->id,
            'nisn' => $request->nisn,
            'nama_lengkap' => $request->name, 
            'jenis_kelamin' => $request->jenis_kelamin,
            'kelas_id' => $request->kelas_id,          
            'tingkat' => $kelas ? $kelas->tingkat : 7, 
            'tahun_ajaran_id' => $tahunAktif ? $tahunAktif->id : null, 
            'status' => 'Aktif',
        ]);

        return back()->with('success', "Siswa {$request->name} ditambahkan. Password default: {$request->nisn}");
    }

    // === 3. IMPORT SISWA ===
    public function importSiswa(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        
        try {
            Excel::import(new SiswaImport, $request->file('file'));
            return back()->with('success', 'Import Siswa Berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    // === 4. DOWNLOAD TEMPLATE ===
    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport, 'template_siswa.xlsx');
    }

    // === 5. SIMPAN GURU ===
    public function storeGuru(Request $request)
    {
        $request->validate(['name' => 'required', 'email' => 'required|email|unique:users']);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'guru',
            'password' => Hash::make('123456'),
            'must_change_password' => false,
        ]);

        return back()->with('success', 'Akun Guru berhasil dibuat!');
    }

    // === 6. IMPORT GURU ===
    public function importGuru(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        
        try {
            Excel::import(new GuruImport, $request->file('file'));
            return back()->with('success', 'Import Guru Berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    // === 7. UBAH PASSWORD ADMIN ===
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

    // === 8. HAPUS USER ===
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Karena cascade, data siswa/guru ikut terhapus
        return back()->with('success', 'User berhasil dihapus.');
    }

    // === 9. FITUR RESET PASSWORD (SOLUSI JIKA SISWA LUPA/AKUN DIBAJAK) ===
    // Pastikan route-nya sudah dibuat di web.php: Route::post('/admin/siswa/{id}/reset', ...)
    public function resetPasswordSiswa($id)
    {
        // 1. Cari data siswa
        $siswa = Siswa::findOrFail($id);
        
        // 2. Cek apakah siswa punya akun user
        if ($siswa->user) {
            // 3. Reset Password kembali ke NISN
            $siswa->user->update([
                'password' => Hash::make($siswa->nisn), // Password jadi NISN lagi
                'must_change_password' => true,         // Paksa ganti password saat login nanti
            ]);

            return back()->with('success', "Password siswa a.n {$siswa->nama_lengkap} berhasil di-reset kembali ke NISN ({$siswa->nisn}).");
        }

        return back()->with('error', 'Akun user tidak ditemukan untuk siswa ini.');
    }
}