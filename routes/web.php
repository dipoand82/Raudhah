<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\ProfilSekolahController;
use App\Models\ProfilSekolah; // <--- PENTING: Panggil Model

Route::get('/', function () {
    $profil_sekolah = ProfilSekolah::first(); // <--- Ambil data profil sekolah
    return view('welcome', compact('profil_sekolah'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'force.change.password'])
  ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

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

        // === PROFIL SEKOLAH ===
        Route::controller(ProfilSekolahController::class)
            ->prefix('profil')
            ->name('profil.')
            ->group(function () {
                Route::get('/', 'edit')->name('edit');
                Route::patch('/', 'update')->name('update');
            });
});
