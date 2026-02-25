<?php

use App\Http\Controllers\Admin\GaleriController as AdminGaleri;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ManajemenUserController;
use App\Http\Controllers\Admin\MasterTagihanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ProfilSekolahController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TagihanSiswaController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\DashboardController;  
use App\Http\Controllers\GaleriController as PublicGaleri;
use App\Http\Controllers\ProfileController;
use App\Models\ProfilSekolah;
use Illuminate\Support\Facades\Route;

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

Route::get('/galeri-kegiatan', [PublicGaleri::class, 'index'])->name('galeri.index');
Route::get('/galeri-kegiatan/{id}', [PublicGaleri::class, 'show'])->name('galeri.show');
Route::get('/informasi-sekolah', function () {
    $profil_sekolah = ProfilSekolah::first();

    return view('info.index', compact('profil_sekolah'));
})->name('info.index');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/sekolah', [ProfileController::class, 'updateSekolah'])->name('profile.sekolah.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA (PUSAT KONTROL)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. MANAJEMEN USER (TAB SISWA & GURU)
    Route::prefix('manajemen-user')->name('manajemen-user.')->group(function () {

        // SATU-SATUNYA rute GET Utama (Pusat Search Guru & Siswa)
        Route::get('/', [ManajemenUserController::class, 'index'])->name('index');

        // RUTE GURU (Proses)
        Route::prefix('gurus')->name('gurus.')->controller(GuruController::class)->group(function () {
            Route::post('/store', 'store')->name('store');
            Route::post('/import', 'import')->name('import');
            Route::put('/{id}', 'update')->name('update');
            Route::get('/template', 'downloadTemplate')->name('template');
        });

        // RUTE SISWA (Proses & Bulk Action)
        Route::controller(ManajemenUserController::class)->group(function () {
            Route::post('/siswa/store', 'storeSiswa')->name('siswa.store');
            Route::post('/siswa/import', 'importSiswa')->name('siswa.import');
            Route::post('/siswa/{id}/reset', 'resetPasswordSiswa')->name('siswa.reset');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::put('/password', 'updatePassword')->name('password.update');
        });

        // RUTE BULK & TEMPLATE SISWA (Arahkan ke SiswaController agar fungsi JS kamu jalan)
        Route::get('/siswa/template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');
        Route::delete('/siswa/bulk-delete', [SiswaController::class, 'bulkDestroy'])->name('siswa.bulk_delete');
        Route::patch('/siswa/bulk-reset', [SiswaController::class, 'bulkResetPassword'])->name('siswa.bulk_reset');
    });

    // 2. DATA SISWA (DETAIL AKADEMIK)
    Route::controller(SiswaController::class)->prefix('data-siswa')->name('siswas.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::post('/import', 'import')->name('import');
        Route::get('/export', 'export')->name('export');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/bulk-delete', 'bulkDestroy')->name('bulk_delete'); // Bulk delete versi Akademik
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // 3. FITUR PENUNJANG
    Route::resource('galeri', AdminGaleri::class);

    Route::controller(ProfilSekolahController::class)->prefix('profil')->name('profil.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
    });

    Route::controller(TahunAjaranController::class)->prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('/{id}/activate', 'activate')->name('activate');
        Route::post('/graduation', 'graduation')->name('graduation');
    });

    Route::controller(KelasController::class)->prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // 4. KEUANGAN & PEMBAYARAN (Grup Tunggal - JANGAN DIDUPLIKASI)
    // 4. KEUANGAN & PEMBAYARAN (Grup Tunggal)
    // 4. KEUANGAN & PEMBAYARAN (Grup Tunggal)
    // Note: Kita hapus .admin. di name rute grup ini karena sudah ada di pembungkus luar
    // Pastikan ini berada di dalam Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () { ...

    Route::prefix('keuangan')->name('keuangan.')->group(function () {

        // A. Master Tagihan (Step 1)
        // Jika tetap pakai names('master'), maka di Blade panggil: admin.keuangan.master.index / .store
        Route::resource('master-tagihan', MasterTagihanController::class)->names('master');

        // B. Tagihan Siswa (Step 2)
        Route::get('tagihan/create-bulk', [TagihanSiswaController::class, 'createBulk'])->name('tagihan.create-bulk');
        Route::post('tagihan/store-bulk', [TagihanSiswaController::class, 'storeBulk'])->name('tagihan.store-bulk');
        Route::delete('tagihan/destroy-bulk', [TagihanSiswaController::class, 'destroyBulk'])->name('tagihan.destroy-bulk');
        Route::resource('tagihan', TagihanSiswaController::class);

        // C. Pembayaran (Step 3)
        Route::get('pembayaran/search', [PembayaranController::class, 'search'])->name('pembayaran.search');
        Route::get('pembayaran/{id}/cetak', [PembayaranController::class, 'cetakKuitansi'])->name('pembayaran.cetak');
        Route::resource('pembayaran', PembayaranController::class)->only(['index', 'store']);
        // D. Laporan (Step Akhir)
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
        // Route::get('laporan',         [LaporanController::class, 'index'])->name('admin.keuangan.laporan.index');
        // Route::get('laporan/export',  [LaporanController::class, 'export'])->name('admin.keuangan.laporan.export');

    });
});
