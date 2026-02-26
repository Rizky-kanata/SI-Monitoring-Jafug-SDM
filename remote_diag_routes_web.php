<?php

use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDiagramController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\RiibSsoController;
use App\Http\Controllers\Api\DiagramController;
use App\Http\Controllers\Api\DiagramStatsController;
use App\Http\Controllers\Api\DiagramTemplateController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/sso/admin', RiibSsoController::class)->name('sso.admin');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/docs', DocsController::class)->name('docs');

    Route::middleware('can:access-admin-panel')->group(function (): void {
        Route::get('/admin/panel', AdminPanelController::class)->name('admin.panel');
        Route::delete('/admin/diagrams/{diagram}', [AdminDiagramController::class, 'destroy'])
            ->name('admin.diagrams.destroy');
        Route::get('/admin/panel-riib', function () {
            return redirect()->away(config('services.riib_admin.url'));
        })->name('admin.riib');
    });

    Route::prefix('api')->name('api.')->group(function (): void {
        Route::get('/status', fn () => ['status' => 'ok'])->name('status');
        Route::get('/stats', DiagramStatsController::class)->name('stats');
        Route::get('/template', [DiagramTemplateController::class, 'show'])->name('template.show');
        Route::post('/template', [DiagramTemplateController::class, 'store'])
            ->middleware('can:access-admin-panel')
            ->name('template.store');

        Route::apiResource('diagrams', DiagramController::class);
    });
});
