<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'block.suspicious.ips' => \App\Http\Middleware\BlockSuspiciousIPs::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'auth.secure' => \App\Http\Middleware\SecureSessionMiddleware::class,
            'redirect.dashboard' => \App\Http\Middleware\RedirectToDashboard::class,
        ]);
        
        // Apply middleware globally to web routes
        $middleware->web(append: [
            \App\Http\Middleware\BlockSuspiciousIPs::class,
            \App\Http\Middleware\SecureSessionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
