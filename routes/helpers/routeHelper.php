
<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('registerFormRoutes')) {
    function registerFormRoutes(string $prefix, string $controller): void
    {
        // ROUTE KHUSUS HARUS DIDAFTARKAN DULU
        Route::prefix($prefix)->name("$prefix.")->group(function () use ($controller) {

            if (method_exists($controller, 'verification')) {
                Route::get('verification', [$controller, 'verification'])
                    ->name('verification');
            }

            if (method_exists($controller, 'updateVerification')) {
                Route::put('verification/{uuid}', [$controller, 'updateVerification'])
                    ->name('verification.update');
            }

            // ROUTE AUDIT
            if (method_exists($controller, 'auditIndex')) {
                Route::get('audit-data', [$controller, 'auditIndex'])
                    ->name('audit');
            }

            if (method_exists($controller, 'copyToAudit')) {
                Route::post('copy-to-audit/{uuid}', [$controller, 'copyToAudit'])
                    ->name('copy-to-audit');
            }

            // EXPORT PDF
            if (method_exists($controller, 'exportPdf')) {
                Route::get('export-pdf', [$controller, 'exportPdf'])
                    ->name('exportPdf');
            }

            // EXPORT
            if (method_exists($controller, 'export')) {
                Route::get('export', [$controller, 'export'])
                    ->name('export');
            }
        });

        // RESOURCE TERAKHIR
        Route::resource($prefix, $controller)
            ->parameters([$prefix => 'uuid']);
    }
}

if (!function_exists('registerRecycleRoutes')) {
    function registerRecycleRoutes(string $prefix, string $controller): void
    {
        Route::prefix($prefix)->name("$prefix.")->group(function () use ($controller) {

            if (method_exists($controller, 'recyclebin')) {
                Route::get('recycle-bin', [$controller, 'recyclebin'])
                    ->name('recyclebin');
            }

            if (method_exists($controller, 'restore')) {
                Route::post('restore/{uuid}', [$controller, 'restore'])
                    ->name('restore');
            }

            if (method_exists($controller, 'deletePermanent')) {
                Route::delete('delete-permanent/{uuid}', [$controller, 'deletePermanent'])
                    ->name('deletePermanent');
            }
        });
    }
}
