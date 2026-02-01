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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // <--- WAJIB TAMBAHKAN INI (Baris Penting)

class ManajemenUserController extends Controller
{
    // === 1. HALAMAN UTAMA (TABS) ===
    public function index(Request $request)
    {  
        $search = $request->input('search');
        // Menggunakan default 10 jika tidak ada input per_page
        $perPage = $request->input('per_page', 30); 

        // 1. TAMBAHKAN INI: Ambil input dari filter dropdown di Blade
        $statusFilter = $request->input('status'); // TAMBAHKAN INI
        $kelasFilter = $request->input('kelas_id'); // TAMBAHKAN INI
        // ==========================================================
        // A. Ambil Data User Role SISWA (LOGIKA BARU - JOIN & SORT)
        // ==========================================================
        $userSiswa = User::where('role', 'siswa')
            // 1. Join ke tabel siswas untuk akses NISN & STATUS
            ->join('siswas', 'users.id', '=', 'siswas.user_id')
            // 2. Left Join ke kelas untuk sorting berdasarkan tingkat/nama kelas
            ->leftJoin('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            // 3. Select users.* agar output tetap berupa model User
            ->select('users.*')
            
            // Logika Pencarian (TETAP)
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('siswas.nisn', 'like', "%{$search}%");
                });
            })
            // 2. TAMBAHKAN INI: Filter berdasarkan Status jika dipilih
            ->when($statusFilter, function($q) use ($statusFilter) { // TAMBAHKAN INI
                return $q->where('siswas.status', $statusFilter);    // TAMBAHKAN INI
            })                                                      // TAMBAHKAN INI

            // 3. TAMBAHKAN INI: Filter berdasarkan Kelas jika dipilih
            ->when($kelasFilter, function($q) use ($kelasFilter) {   // TAMBAHKAN INI
                return $q->where('siswas.kelas_id', $kelasFilter);   // TAMBAHKAN INI
            })
            
            // --- CUSTOM SORTING START ---
            // Logika: Urutkan status (Aktif -> Cuti -> Keluar) terlebih dahulu
            ->orderByRaw("FIELD(siswas.status, 'Aktif', 'Cuti','Lulus','Pindah', 'Keluar') ASC")
            // --- CUSTOM SORTING END ---
            
            // Logika Pengurutan Lama (Tingkat -> Nama Kelas -> Nama Siswa)
            ->orderBy('kelas.tingkat', 'asc')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('users.name', 'asc')
            
            // Eager Load relasi untuk keperluan view
            ->with('dataSiswa.kelas') 
            ->paginate($perPage, ['*'], 'siswa_page');

        // ==========================================================
        // B. Ambil Data User Role GURU (TETAP SAMA)
        // ==========================================================
        $userGuru = User::where('role', 'guru')
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(30, ['*'], 'guru_page');

        // ==========================================================
        // C. Data Pendukung Modal (TETAP SAMA)
        // ==========================================================
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(); 
        
        // Untuk modal TAMBAH (ambil satu yg aktif)
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        // Untuk modal EDIT (ambil semua untuk dropdown)
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->get(); 

        return view('admin.manajemen_user.index', compact(
            'userSiswa', 
            'userGuru', 
            'kelas', 
            'tahunAjaran', 
            'tahunAjaranList'
        ));
    }
    public function storeSiswa(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|numeric|unique:siswas,nisn',
            'email' => 'nullable|email|unique:users,email',
            'kelas_id' => 'nullable|exists:kelas,id', 
            'jenis_kelamin' => 'required|in:L,P',     
        ]);

        // 2. Ambil Data Pendukung
        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        // 3. LOGIKA EMAIL OTOMATIS
        if ($request->filled('email')) {
            $emailFinal = $request->email;
        } else {
            // Ambil kata pertama & ubah ke lowercase
            $namaDepan = Str::lower(explode(' ', trim($request->name))[0]);
            
            // Format: namadepan.nisn@raudhah.com
            $emailFinal = $namaDepan . '.' . $request->nisn . '@raudhah.com';
            
            // Cek duplikasi email hasil generate
            if (User::where('email', $emailFinal)->exists()) {
                $emailFinal = $namaDepan . $request->nisn . rand(1, 9) . '@raudhah.com';
            }
        }

        // 4. Eksekusi dengan Database Transaction (Sangat Disarankan)
        return DB::transaction(function () use ($request, $emailFinal, $tahunAktif) {
            
            // 5. Create/Update User (Akun Login)
            // Menggunakan updateOrCreate agar lebih "tahan banting" terhadap error data ganda
            $user = User::updateOrCreate(
                ['email' => $emailFinal],
                [
                    'name' => $request->name,
                    'password' => Hash::make($request->nisn),
                    'role' => 'siswa',
                    'must_change_password' => true,
                ]
            );

            // 6. Create/Update Data Siswa
            Siswa::updateOrCreate(
                ['nisn' => $request->nisn],
                [
                    'user_id'         => $user->id,
                    'nama_lengkap'    => $request->name,
                    'jenis_kelamin'   => $request->jenis_kelamin,
                    'kelas_id'        => $request->kelas_id,
                    'tingkat'         => $request->kelas_id ? Kelas::find($request->kelas_id)->tingkat : null,
                    'tahun_ajaran_id' => $tahunAktif ? $tahunAktif->id : null,
                    'status'          => 'Aktif',
                ]
            );

            return redirect()->back()->with('success', "Siswa {$request->name} berhasil ditambahkan! Email login: " . $emailFinal);
        });
    }
    // === 3. IMPORT SISWA ===
 // === 3. IMPORT SISWA ===
public function importSiswa(Request $request)
{
    $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
    
    try {
        $import = new \App\Imports\SiswaImport;
        $import->import($request->file('file'));

        // Simpan info fallback ke session (info biru)
        if (!empty($import->fallbackClasses)) {
            session()->flash('fallback_info', $import->fallbackClasses);
        }

        // Cek jika ada kegagalan validasi (info merah)
        if ($import->failures()->isNotEmpty()) {
            // Kita gunakan flash agar bisa digabung dengan info biru di atas
            return back()->with('import_errors', $import->failures());
        }

        return back()->with('success', 'Import Berhasil Selesai!');
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal Total: ' . $e->getMessage());
    }
}

    // === 4. DOWNLOAD TEMPLATE ===
    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport, 'template_import_siswa.xlsx');
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
        return back()->with('success', 'User berhasil dihapus');
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

            return back()->with('success', "Password siswa {$siswa->nama_lengkap} berhasil di-reset kembali ke NISN ({$siswa->nisn}).");
        }

        return back()->with('error', 'Akun user tidak ditemukan untuk siswa ini.');
    }
}