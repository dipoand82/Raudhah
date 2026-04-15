<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
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
use App\Http\Controllers\GaleriController as PublicGaleri;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\KeuanganController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Models\ProfilSekolah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
// Route Webhook Midtrans
Route::post('/webhook/midtrans', [\App\Http\Controllers\MidtransWebhookController::class, 'handle'])
    ->name('webhook.midtrans');

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

// PUSAT REDIRECT SETELAH LOGIN
Route::get('/dashboard', function () {
    $role = Auth::user()->role;

    if ($role === 'siswa') {
        return redirect()->route('siswa.dashboard');
    }

    // PERBAIKAN: Guru sekarang diarahkan ke dashboard admin
    if ($role === 'guru' || $role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    abort(403);
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTH & PROFILE ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/force-password-change', [ForcePasswordChangeController::class, 'index'])->name('force.password.change');
    Route::post('/force-password-change', [ForcePasswordChangeController::class, 'update'])->name('force.password.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/sekolah', [ProfileController::class, 'updateSekolah'])->name('profile.sekolah.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN & GURU AREA (SATU PINTU)
|--------------------------------------------------------------------------
*/
// Keduanya bisa masuk ke awalan /admin
Route::middleware(['auth', 'verified', 'role:admin,guru'])->prefix('admin')->name('admin.')->group(function () {

    // 1. DASHBOARD (Bersama)
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // 2. DATA SISWA (Bersama)
    Route::controller(SiswaController::class)->prefix('data-siswa')->name('siswas.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::post('/import', 'import')->name('import');
        Route::get('/export', 'export')->name('export');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/bulk-delete', 'bulkDestroy')->name('bulk_delete');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // 3. LAPORAN KEUANGAN (Bersama)
    Route::prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    });

    /*
    |--------------------------------------------------------------------------
    | KHUSUS ADMIN (GURU DIBLOKIR DI SINI)
    |--------------------------------------------------------------------------
    */
    // Sub-grup ini menggunakan middleware khusus role:admin
    Route::middleware(['role:admin'])->group(function () {

        // 1. Manajemen User
        Route::prefix('manajemen-user')->name('manajemen-user.')->group(function () {
            Route::get('/', [ManajemenUserController::class, 'index'])->name('index');

            // Guru
            Route::prefix('gurus')->name('gurus.')->controller(GuruController::class)->group(function () {
                Route::post('/store', 'store')->name('store');
                Route::post('/import', 'import')->name('import');
                Route::put('/{id}', 'update')->name('update');
                Route::get('/template', 'downloadTemplate')->name('template');
            });

            // Siswa (Manajemen Akun)
            Route::controller(ManajemenUserController::class)->group(function () {
                Route::post('/siswa/store', 'storeSiswa')->name('siswa.store');
                Route::post('/siswa/import', 'importSiswa')->name('siswa.import');
                Route::post('/siswa/{id}/reset', 'resetPasswordSiswa')->name('siswa.reset');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::put('/password', 'updatePassword')->name('password.update');
            });

            // Bulk Action & Template Siswa
            Route::get('/siswa/template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');
            Route::delete('/siswa/bulk-delete', [SiswaController::class, 'bulkDestroy'])->name('siswa.bulk_delete');
            Route::patch('/siswa/bulk-reset', [SiswaController::class, 'bulkResetPassword'])->name('siswa.bulk_reset');
        });

        // 2. Pengaturan Sistem & Fitur Penunjang
        Route::resource('galeri', AdminGaleri::class);

        Route::controller(ProfilSekolahController::class)->prefix('profil')->name('profil.')->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
        });

        Route::resource('tahun-ajaran', TahunAjaranController::class);
        Route::post('tahun-ajaran/{id}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');
        Route::post('tahun-ajaran/graduation', [TahunAjaranController::class, 'graduation'])->name('tahun-ajaran.graduation');

        Route::resource('kelas', KelasController::class);

        // 3. Transaksi Keuangan (Selain Laporan)
        Route::prefix('keuangan')->name('keuangan.')->group(function () {
            Route::resource('master-tagihan', MasterTagihanController::class)->names('master');

            Route::get('tagihan/create-bulk', [TagihanSiswaController::class, 'createBulk'])->name('tagihan.create-bulk');
            Route::post('tagihan/store-bulk', [TagihanSiswaController::class, 'storeBulk'])->name('tagihan.store-bulk');
            Route::delete('tagihan/destroy-bulk', [TagihanSiswaController::class, 'destroyBulk'])->name('tagihan.destroy-bulk');
            Route::resource('tagihan', TagihanSiswaController::class);

            Route::get('pembayaran/search', [PembayaranController::class, 'search'])->name('pembayaran.search');
            Route::get('pembayaran/{id}/cetak', [PembayaranController::class, 'cetakKuitansi'])->name('pembayaran.cetak');
            Route::resource('pembayaran', PembayaranController::class)->only(['index', 'store']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| SISWA AREA
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'verified',
    'role:siswa',
    EnsurePasswordIsChanged::class
])
->prefix('siswa')
->name('siswa.')
->group(function () {
    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');

    Route::prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('/riwayat', [KeuanganController::class, 'riwayat'])->name('riwayat');
        Route::post('/snap-token/{tagihan}', [KeuanganController::class, 'getSnapToken'])->name('snap-token');
        Route::get('/pembayaran/detail-sukses', [KeuanganController::class, 'getDetailSukses'])->name('detail-sukses');
    });
});
