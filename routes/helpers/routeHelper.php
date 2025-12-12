<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('registerFormRoutes')) {
    /**
     * Mendaftarkan route otomatis untuk modul dengan fitur tambahan
     *
     * @param string $prefix
     * @param string $controller
     */
    function registerFormRoutes(string $prefix, string $controller): void
    {
        // Hindari duplikasi resource
        if (Route::has("$prefix.index")) {
            return;
        }

        // Prefix dan nama group
        Route::prefix($prefix)->name("$prefix.")->group(function () use ($controller, $prefix) {

            // ✅ Tambahan route: verifikasi
            if (method_exists($controller, 'verification')) {
                Route::get('verification', [$controller, 'verification'])->name('verification');
            }
            if (method_exists($controller, 'updateVerification')) {
                Route::put('verification/{uuid}', [$controller, 'updateVerification'])->name('verification.update');
            }

            // ✅ Tambahan route: export PDF
            if (method_exists($controller, 'exportPdf')) {
                Route::get('export-pdf', [$controller, 'exportPdf'])->name('exportPdf');
            }

            // ✅ Tambahan route: export Excel/CSV
            if (method_exists($controller, 'export')) {
                Route::get('export', [$controller, 'export'])->name('export');
            }
        });

        // ✅ Resource utama (CRUD)
        Route::resource($prefix, $controller)->parameters([
            $prefix => 'uuid'
        ]);
    }
}
