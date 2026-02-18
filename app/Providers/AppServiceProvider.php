<?php

namespace App\Providers;

use App\Models\User;
use App\Models\ProfilSekolah;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // 1. Sharing data profil ke semua view
        View::composer('*', function ($view) {
            $profil = ProfilSekolah::first();
            $view->with('profil_sekolah', $profil);
        });

        // 2. Registrasi Gate Admin
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        // 3. Registrasi Gate Guru (Opsional, jika butuh nanti)
        Gate::define('guru', function (User $user) {
            return $user->role === 'guru';
        });
    }
}
