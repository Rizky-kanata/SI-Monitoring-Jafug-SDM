<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KepangkatanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    Route::get('/diagram-generator-link', function () {
        $target = config('services.diagram_generator.url', '/diagram-generator/public/index.php');

        if (! filter_var($target, FILTER_VALIDATE_URL)) {
            $target = url($target);
        }

        return redirect()->away($target);
    })->name('diagram.generator');
});
