<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // Ubah parameter menjadi ...$roles agar bisa menerima lebih dari satu role sekaligus
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user saat ini ada di dalam kumpulan role yang diizinkan rute
        // $roles sekarang berisi array, misalnya: ['admin', 'guru']
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Maaf, Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
