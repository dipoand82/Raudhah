<?php

namespace App\Providers;

use App\Models\ProfilSekolah;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL; // Tambahkan ini di atas
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // PERBAIKAN: Cek host yang sedang diakses secara langsung, bukan cuma config
        if (str_contains(request()->getHost(), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }
        if (str_contains(env('APP_URL'), 'ngrok')) {
            URL::forceScheme('https');
        }

        // 1. Sharing data profil ke semua view
        View::composer('*', function ($view) {
            $profil = ProfilSekolah::first();
            $view->with('profil_sekolah', $profil);
        });

        // 2. Registrasi Gate Admin
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        // 3. Registrasi Gate Guru
        Gate::define('guru', function (User $user) {
            return $user->role === 'guru';
        });

        // Opsional: Gunakan Bootstrap untuk Pagination
        // Paginator::useBootstrapFour();
    }
}
