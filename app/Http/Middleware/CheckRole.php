<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user di database sesuai dengan yang diminta di route
        if (Auth::user()->role !== $role) {
            abort(403, 'Maaf, Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
