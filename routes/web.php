<?php

use Illuminate\Support\Facades\Route;
use App\Models\ProfilSekolah;

// Import Controller (Admin)
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\ManajemenUserController;
use App\Http\Controllers\Admin\ProfilSekolahController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleri;
use App\Http\Controllers\GaleriController as PublicGaleri;
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $profil_sekolah = ProfilSekolah::first();
    $galeri = \App\Models\Galeri::latest()->take(4)->get();
    return view('welcome', compact('profil_sekolah', 'galeri'));
});
// TAMBAHKAN DUA BARIS INI:
Route::get('/galeri-kegiatan', [PublicGaleri::class, 'index'])->name('galeri.index');
Route::get('/galeri-kegiatan/{id}', [PublicGaleri::class, 'show'])->name('galeri.show');
// RUTE PUBLIK UNTUK INFO (Arahkan ke resources/views/info/index.blade.php)
Route::get('/informasi-sekolah', function () {
    $profil_sekolah = ProfilSekolah::first();
    return view('info.index', compact('profil_sekolah'));
})->name('info.index');
/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/sekolah', [ProfileController::class, 'updateSekolah'])->name('profile.sekolah.update');
});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ==========================================================
        // 1. MANAJEMEN USER (PUSAT AKUN: TAB SISWA, GURU, PASSWORD)
        // ==========================================================
        // Controller: App\Http\Controllers\Admin\ManajemenUserController
        Route::controller(ManajemenUserController::class)
            ->prefix('manajemen-user')
            ->name('manajemen-user.') // Prefix nama route jadi 'admin.manajemen-user.' (asumsi ada group admin di luarnya)
            ->group(function () {

                // Halaman Utama
                Route::get('/', 'index')->name('index');

                // --- SISWA ---
                Route::post('/siswa/store', 'storeSiswa')->name('siswa.store');
                Route::post('/siswa/import', 'importSiswa')->name('siswa.import');

                // [BARU] Reset Password Siswa
                // URL Akhir: /admin/manajemen-user/siswa/{id}/reset
                // Nama Route: admin.manajemen-user.siswa.reset
                Route::post('/siswa/{id}/reset', 'resetPasswordSiswa')->name('siswa.reset');

                // --- GURU ---
                Route::post('/guru/store', 'storeGuru')->name('guru.store');
                Route::post('/guru/import', 'importGuru')->name('guru.import');

                // --- UTILITY ---
                Route::put('/password', 'updatePassword')->name('password.update');
                Route::delete('/{id}', 'destroy')->name('destroy');

                // Download Template (Pengecualian: Beda Controller)
                // Karena beda controller, harus tulis lengkap [Class, method]
                Route::get('/siswa/template', [\App\Http\Controllers\Admin\SiswaController::class, 'downloadTemplate'])
                    ->name('siswa.template');
            });
            //     Route::get('/siswa/template', [SiswaController::class, 'downloadTemplate'])
            // ->name('siswa.template');

        // ==========================================================
        // 2. DATA SISWA (DETAIL AKADEMIK)
        // ==========================================================
        // Controller: App\Http\Controllers\Admin\SiswaController
        Route::controller(SiswaController::class)
            ->prefix('data-siswa')
            ->name('siswas.')
            ->group(function () {
                Route::get('/', 'index')->name('index');        // Tabel Detail
                Route::get('/create', 'create')->name('create');// Form Tambah
                Route::post('/', 'store')->name('store');       // Simpan
                Route::post('/import', 'import')->name('import'); // Import (Jika ada menu terpisah)
                Route::get('/export', 'export')->name('export');
                Route::get('/{id}/edit', 'edit')->name('edit'); // Edit Lengkap
                Route::put('/{id}', 'update')->name('update');  // Update
                Route::delete('/bulk-delete', 'bulkDestroy')->name('bulk_delete');
                Route::delete('/{id}', 'destroy')->name('destroy'); // Hapus
            });


        // ==========================================================
        // 3. LAIN-LAIN (Profil Sekolah, Kelas, Tahun Ajaran)
        // ==========================================================

        // 3. [BARU] KHUSUS GALERI (Berdiri Sendiri)
        // Ini lebih bagus daripada ditaruh di dalam Profil atau Manajemen User
        Route::resource('galeri', AdminGaleri::class);

        // PROFIL SEKOLAH
        Route::controller(ProfilSekolahController::class)
            ->prefix('profil')
            ->name('profil.')
            ->group(function () {
                Route::get('/', 'edit')->name('edit');
                Route::patch('/', 'update')->name('update');
            });

        // TAHUN AJARAN
        Route::controller(TahunAjaranController::class)
            ->prefix('tahun-ajaran')
            ->name('tahun-ajaran.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::post('/{id}/activate', 'activate')->name('activate');
                Route::post('/graduation', 'graduation')->name('graduation');
            });

        // KELAS
        Route::controller(KelasController::class)
            ->prefix('kelas')
            ->name('kelas.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{id}', 'update')->name('update'); // Pakai {id} biar aman
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

    });
