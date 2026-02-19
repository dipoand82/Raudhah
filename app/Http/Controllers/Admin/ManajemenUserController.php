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
    // 1. Tangkap input dari Blade
    $searchSiswa = $request->input('search');
    $searchGuru = $request->input('search_guru');
    $perPageSiswa = $request->input('per_page', 30);
    $perPageGuru = $request->input('per_page_guru', 30);
    $statusFilter = $request->input('status');
    $kelasFilter = $request->input('kelas_id');

    // --- QUERY SISWA (VERSI ANTI-DUPLIKAT) ---
    $userSiswa = User::where('role', 'siswa')
        ->with(['dataSiswa.kelas']) // Eager Loading relasi
        // Filter Pencarian (Nama di tabel Users atau NISN di tabel Siswas)
        ->when($searchSiswa, function($q) use ($searchSiswa) {
            $q->where(function($query) use ($searchSiswa) {
                $query->where('name', 'like', "%{$searchSiswa}%")
                      ->orWhereHas('dataSiswa', function($sq) use ($searchSiswa) {
                          $sq->where('nisn', 'like', "%{$searchSiswa}%");
                      });
            });
        })
        // Filter Status (Ada di tabel Siswas)
        ->when($statusFilter, function($q) use ($statusFilter) {
            return $q->whereHas('dataSiswa', function($sq) use ($statusFilter) {
                $sq->where('status', $statusFilter);
            });
        })
        // Filter Kelas (Ada di tabel Siswas)
        ->when($kelasFilter, function($q) use ($kelasFilter) {
            return $q->whereHas('dataSiswa', function($sq) use ($kelasFilter) {
                $sq->where('kelas_id', $kelasFilter);
            });
        })
        // Sorting: Karena kita tidak pakai JOIN, kita gunakan Join khusus untuk sorting
        // agar tidak merusak data utama atau pakai cara manual di bawah:
        ->join('siswas', 'users.id', '=', 'siswas.user_id')
        ->leftJoin('kelas', 'siswas.kelas_id', '=', 'kelas.id')
        ->select('users.*') // Tetap select users.* agar tidak ganda
        ->orderByRaw("FIELD(siswas.status, 'Aktif', 'Cuti','Lulus','Pindah', 'Keluar') ASC")
        ->orderBy('kelas.tingkat', 'asc')
        ->orderBy('kelas.nama_kelas', 'asc')
        ->orderBy('users.name', 'asc')
        ->paginate($perPageSiswa, ['*'], 'siswa_page')
        ->withQueryString();

    // --- QUERY GURU (TETAP SAMA) ---
    $userGuru = User::where('role', 'guru')
        ->when($searchGuru, function($q) use ($searchGuru) {
            $q->where(function($query) use ($searchGuru) {
                $query->where('name', 'like', "%{$searchGuru}%")
                      ->orWhere('email', 'like', "%{$searchGuru}%");
            });
        })
        ->latest()
        ->paginate($perPageGuru, ['*'], 'guru_page')
        ->withQueryString();

    // --- DATA PENDUKUNG ---
    $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
    $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->get();
    $tahunAjaran = TahunAjaran::where('is_active', true)->first();

    return view('admin.manajemen_user.index', compact(
        'userSiswa', 'userGuru', 'kelas', 'tahunAjaran', 'tahunAjaranList'
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
    // 1. Cari user-nya dulu
    $user = User::findOrFail($id);

    // 2. Simpan role-nya ke variabel (untuk tahu ini siswa atau guru)
    $role = $user->role;

    // 3. Hapus user
    $user->delete();

    // 4. Redirect balik dengan membawa 'active_tab'
    return back()->with([
        'success' => 'User berhasil dihapus',
        'active_tab' => $role // Mengirim 'siswa' atau 'guru' ke session
    ]);
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
