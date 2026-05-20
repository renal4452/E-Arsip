<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole; // ✅ Import middleware Role kita

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // ✅ 1. Daftarkan alias middleware kustom kita
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        // ❌ 2. HandleInertiaRequests sudah dibumihanguskan dari grup web!
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();