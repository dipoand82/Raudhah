<?php

namespace App\Providers;

use App\Models\ProfilSekolah;
use Illuminate\Support\ServiceProvider; // Sesuaikan dengan nama modelmu
use Illuminate\Support\Facades\View; // <--- TAMBAHKAN BARIS INI
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $profil = ProfilSekolah::first(); // Ambil data pertama dari database
            $view->with('profil_sekolah', $profil);
        });
    }
}
