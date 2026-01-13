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
use App\Http\Controllers\Admin\KelasController; // <-- Ditambahkan agar rapi
use App\Http\Controllers\Admin\UserController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $profil_sekolah = ProfilSekolah::first();
    return view('welcome', compact('profil_sekolah'));
});

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

    // Route tambahan untuk Update Data Sekolah dari halaman Profile
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

        // // === GURU ===
        // Route::controller(GuruController::class)
        //     ->prefix('guru')
        //     ->name('gurus.')
        //     ->group(function () {
        //         Route::get('/', 'index')->name('index');
        //         Route::get('/create', 'create')->name('create');
        //         Route::post('/', 'store')->name('store');
        //         Route::post('/import', 'import')->name('import');
        //     });

        // // === SISWA ===
        // Route::controller(SiswaController::class)
        //     ->prefix('siswa')
        //     ->name('siswas.')
        //     ->group(function () {
        //         Route::get('/', 'index')->name('index');
        //         Route::get('/create', 'create')->name('create');
        //         Route::post('/', 'store')->name('store');
        //         Route::post('/import', 'import')->name('import');
        //     });

        // GURU
    // Route::controller(App\Http\Controllers\Admin\GuruController::class)
    //     ->prefix('guru')->name('guru.')
    //     ->group(function () {
    //         Route::get('/', 'index')->name('index');
    //         Route::get('/create', 'create')->name('create');
    //         Route::post('/', 'store')->name('store');
    //         Route::post('/import', 'import')->name('import'); // <--- INI WAJIB ADA
    //         Route::delete('/{id}', 'destroy')->name('destroy');
    //     });

    // // SISWA
    // Route::controller(App\Http\Controllers\Admin\SiswaController::class)
    //     ->prefix('siswa')->name('siswas.')
    //     ->group(function () {
    //         Route::get('/', 'index')->name('index');
    //         Route::get('/create', 'create')->name('create');
    //         Route::post('/', 'store')->name('store');
    //         Route::post('/import', 'import')->name('import'); // <--- INI WAJIB ADA
    //         Route::delete('/{id}', 'destroy')->name('destroy');
    //     });

    //     Route::prefix('admin')->name('admin.')->group(function () {

    // === MENU 1: MANAJEMEN USER (PUSAT AKUN) ===
        Route::controller(ManajemenUserController::class)->prefix('manajemen-user')->name('manajemen-user.')->group(function () {
        Route::get('/', 'index')->name('index'); // Halaman Utama (Tabs)
        
        // Aksi Tambah & Import Siswa (Lewat menu ini)
        Route::post('/siswa/store', 'storeSiswa')->name('siswa.store');
        Route::post('/siswa/import', 'importSiswa')->name('siswa.import');
        
        // Aksi Tambah & Import Guru (Lewat menu ini)
        Route::post('/guru/store', 'storeGuru')->name('guru.store');
        Route::post('/guru/import', 'importGuru')->name('guru.import');

        // Ubah Password Admin
        Route::put('/password/update', 'updatePassword')->name('password.update');
        });

    // === MENU 2: DATA SISWA (DETAIL AKADEMIK) ===
    Route::controller(SiswaController::class)->prefix('data-siswa')->name('siswas.')->group(function () {
        Route::get('/', 'index')->name('index');        // Tabel Detail Siswa
        
        // --- TAMBAHAN PENTING (Biar Error Hilang) ---
        Route::get('/create', 'create')->name('create'); // Form Tambah
        Route::post('/', 'store')->name('store');        // Simpan Data
        Route::post('/import', 'import')->name('import'); // <--- INI OBATNYA
        // --------------------------------------------

        Route::get('/{id}/edit', 'edit')->name('edit'); // Form Edit Lengkap
        Route::put('/{id}', 'update')->name('update');  // Proses Update
        Route::delete('/{id}', 'destroy')->name('destroy'); // Hapus
    });

    // ... (Route Kelas & Tahun Ajaran tetap sama) ...


        // === PROFIL SEKOLAH (Admin) ===
        Route::controller(ProfilSekolahController::class)
            ->prefix('profil')
            ->name('profil.')
            ->group(function () {
                Route::get('/', 'edit')->name('edit');
                Route::patch('/', 'update')->name('update');
            });

        // === TAHUN AJARAN & AKADEMIK ===
        // Saya rapikan menggunakan prefix agar kodenya lebih pendek & konsisten
        Route::controller(TahunAjaranController::class)
            ->prefix('tahun-ajaran')          // URL jadi: /admin/tahun-ajaran
            ->name('tahun-ajaran.')           // Name jadi: admin.tahun-ajaran.index
            ->group(function () {
                // CRUD Dasar
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{tahunAjaran}', 'update')->name('update');
                Route::delete('/{tahunAjaran}', 'destroy')->name('destroy');

                // Fitur Khusus: Aktivasi & Kelulusan Massal
                Route::post('/{id}/activate', 'activate')->name('activate');
                Route::post('/luluskan', 'processGraduation')->name('graduation');
            });

        // === MANAJEMEN KELAS ===
        Route::controller(KelasController::class)
            ->prefix('kelas')
            ->name('kelas.')
            ->group(function () {
                Route::get('/', 'index')->name('index');      // Tampilkan Halaman
                Route::post('/', 'store')->name('store');     // Simpan Data
                Route::delete('/{kelas}', 'destroy')->name('destroy'); // Hapus Data
                Route::put('/{kelas}', 'update')->name('update');  // Update Data Kelas
            });

        // === MANAJEMEN USER ===
        Route::controller(UserController::class)
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/', 'index')->name('index');          // Daftar User
                Route::get('/create', 'create')->name('create');  // Form Tambah (PENTING: Ini solusi error 'defined' tadi)
                Route::post('/', 'store')->name('store');         // Simpan User
                Route::get('/{user}/edit', 'edit')->name('edit'); // Form Edit
                Route::put('/{user}', 'update')->name('update');  // Update User
                Route::delete('/{user}', 'destroy')->name('destroy'); // Hapus User
            });

    });