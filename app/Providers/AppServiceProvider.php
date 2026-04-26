<?php

namespace App\Providers;

use App\Models\ProfilSekolah;
use App\Models\User;
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
        if (str_contains(request()->getHost(), 'ngrok-free') || str_contains(config('app.url'), 'ngrok')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $profil = ProfilSekolah::first();
            $view->with('profil_sekolah', $profil);
        });

        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('guru', function (User $user) {
            return $user->role === 'guru';
        });

    }
}
