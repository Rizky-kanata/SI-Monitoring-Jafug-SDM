<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KepangkatanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.attempt');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'showRequest'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('password.otp.send');
    Route::get('/forgot-password/verify/{token}', [PasswordResetController::class, 'showVerify'])->name('password.verify');
    Route::post('/forgot-password/verify', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('profils', ProfilController::class)
        ->names('profils')
        ->except(['show']);

    Route::resource('kepangkatan', KepangkatanController::class)
        ->names('kepangkatan')
        ->except(['show']);
    Route::get('kepangkatan/export/pdf', [KepangkatanController::class, 'exportPdf'])
        ->name('kepangkatan.export');

    Route::get('/diagram-generator-link', function () {
        if (! request()->user()?->isAdmin()) {
            $publicLogin = (string) config('services.diagram_generator.public_login_url', '/diagram-workflow-penelitian-dosen/login');

            if (! filter_var($publicLogin, FILTER_VALIDATE_URL)) {
                if (str_starts_with($publicLogin, '//')) {
                    $publicLogin = request()->getScheme() . ':' . $publicLogin;
                } elseif (str_starts_with($publicLogin, '/')) {
                    $publicLogin = request()->getSchemeAndHttpHost() . $publicLogin;
                } else {
                    $publicLogin = url($publicLogin);
                }
            }

            return redirect()->away($publicLogin);
        }

        $base = rtrim(config('services.diagram_generator.url', '/diagram-generator/public/index.php'), '/');
        $secret = (string) config('services.diagram_generator.sso_secret', '');
        if ($secret === '') {
            $target = "{$base}/login";
        } else {
            $timestamp = now()->timestamp;
            $nonce = Str::random(16);
            $payload = $timestamp . '|' . $nonce;
            $signature = hash_hmac('sha256', $payload, $secret);
            $target = "{$base}/sso/admin?timestamp={$timestamp}&nonce={$nonce}&signature={$signature}";
        }

        if (! filter_var($target, FILTER_VALIDATE_URL)) {
            if (str_starts_with($target, '//')) {
                $target = request()->getScheme() . ':' . $target;
            } elseif (str_starts_with($target, '/')) {
                $target = request()->getSchemeAndHttpHost() . $target;
            } else {
                $target = url($target);
            }
        }

        return redirect()->away($target);
    })->name('diagram.generator');
});
