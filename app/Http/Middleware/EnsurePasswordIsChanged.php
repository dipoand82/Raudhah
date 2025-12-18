<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // LOGIKA PENJAGAAN:
        // 1. User sedang login
        // 2. User ditandai harus ganti password (true)
        // 3. User TIDAK sedang membuka halaman ganti password (biar gak looping)
        if ($user && $user->must_change_password && ! $request->routeIs('password.change.*')) {
            return Redirect::route('password.change.notice');
        }

        return $next($request);
    }
}