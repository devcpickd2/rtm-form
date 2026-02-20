<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // ===== Routing =====
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',       // Route web (browser)
        api: __DIR__ . '/../routes/api.php',       // Route API
        commands: __DIR__ . '/../routes/console.php', // Route untuk command artisan
        health: '/up',                             // Health check endpoint (opsional)
    )

    // ===== Middleware Global =====
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware global, semua request akan melewati sini
        // Contoh default:
        // $middleware->push(\App\Http\Middleware\TrustProxies::class);
        // $middleware->push(\Illuminate\Http\Middleware\HandleCors::class);
        // $middleware->push(\App\Http\Middleware\PreventRequestsDuringMaintenance::class);
        // $middleware->push(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);
        // Tambahkan middleware lain sesuai kebutuhan
    })

    // ===== Exception Handling =====
    ->withExceptions(function (Exceptions $exceptions): void { 
        // Custom exception handling
        // Contoh default Laravel sudah termasuk:
        // - Report exceptions
        // - Render exceptions ke HTTP response
        // Bisa tambahkan handler custom di sini jika perlu
    })

    // ===== Membuat instance aplikasi =====
    ->create();
