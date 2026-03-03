<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
        'force.change.password' => \App\Http\Middleware\EnsurePasswordIsChanged::class,
        'role' => \App\Http\Middleware\CheckRole::class, // TAMBAHKAN BARIS INI
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {  // ✅ isi bagian ini

        $exceptions->render(function (Throwable $e, Request $request) {

            if ($request->expectsJson() || $request->ajax()) {

                if ($e instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tidak valid.',
                        'errors'  => $e->errors(),
                    ], 422);
                }

                return response()->json([
                    'success' => false,
                    'message' => app()->isProduction()
                        ? 'Terjadi kesalahan sistem. Hubungi administrator.'
                        : $e->getMessage(),
                ], 500);
            }
        });

    })->create();
