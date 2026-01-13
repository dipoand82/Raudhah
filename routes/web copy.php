<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\ProfilSekolahController;
use App\Http\Controllers\Admin\TahunAjaranController; // <--- JANGAN LUPA IMPORT INI
use App\Models\ProfilSekolah;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    // Kita pakai variabel '$profil_sekolah' sesuai request Abang
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

        // === GURU ===
        Route::controller(GuruController::class)
            ->prefix('guru')
            ->name('gurus.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::post('/import', 'import')->name('import');
        });

        // === SISWA ===
        Route::controller(SiswaController::class)
            ->prefix('siswa')
            ->name('siswas.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::post('/import', 'import')->name('import');
        });

        // === PROFIL SEKOLAH (Admin) ===
        Route::controller(ProfilSekolahController::class)
            ->prefix('profil')
            ->name('profil.')
            ->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
        });

        // === TAHUN AJARAN & AKADEMIK (BARU) ===
        Route::controller(TahunAjaranController::class)
            ->group(function () {
            // CRUD Dasar
            Route::get('/tahun-ajaran', 'index')->name('tahun-ajaran.index');
            Route::post('/tahun-ajaran', 'store')->name('tahun-ajaran.store');
            Route::put('/tahun-ajaran/{tahunAjaran}', 'update')->name('tahun-ajaran.update');
            Route::delete('/tahun-ajaran/{tahunAjaran}', 'destroy')->name('tahun-ajaran.destroy');

            // Fitur Khusus: Aktivasi & Kelulusan Massal
            Route::post('/tahun-ajaran/{id}/activate', 'activate')->name('tahun-ajaran.activate');
            Route::post('/tahun-ajaran/luluskan', 'processGraduation')->name('tahun-ajaran.graduation');
        });

        // === MANAJEMEN KELAS ===
        Route::controller(App\Http\Controllers\Admin\KelasController::class)
            ->prefix('kelas')
            ->name('kelas.')
            ->group(function () {
            Route::get('/', 'index')->name('index');      // Tampilkan Halaman
            Route::post('/', 'store')->name('store');     // Simpan Data
            Route::delete('/{kelas}', 'destroy')->name('destroy'); // Hapus Data
        });

        Route::controller(UserController::class)
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/', 'index')->name('index');          // Menampilkan daftar user
                Route::get('/create', 'create')->name('create');  // Menampilkan form tambah (opsional jika pakai modal)
                Route::post('/', 'store')->name('store');         // Menyimpan user baru
                Route::get('/{user}/edit', 'edit')->name('edit'); // Menampilkan form edit
                Route::put('/{user}', 'update')->name('update');  // Menyimpan perubahan (Update)
                Route::delete('/{user}', 'destroy')->name('destroy'); // Menghapus user
            });

    });