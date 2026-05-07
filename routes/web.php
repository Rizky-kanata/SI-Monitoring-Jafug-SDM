<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KepangkatanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
    Route::get('/forgot-password/verify', [PasswordResetController::class, 'showVerify'])->name('password.verify');
    Route::get('/forgot-password/verify/{token}', function (Request $request, string $token) {
        $request->session()->put('password_reset_token', $token);

        return redirect()->route('password.verify');
    });
    Route::post('/forgot-password/verify', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    foreach (['portal-sdm', 'beranda-sdm'] as $dashboardAlias) {
        Route::get("/{$dashboardAlias}", DashboardController::class);
    }

    Route::prefix('data-dosen')->name('profils.')->group(function () {
        Route::get('/', [ProfilController::class, 'index'])->name('index');
        Route::get('/create', [ProfilController::class, 'create'])->name('create');
        Route::post('/', [ProfilController::class, 'store'])->name('store');
        Route::get('/template/download', [ProfilController::class, 'downloadTemplate'])->name('template');
        Route::post('/import', [ProfilController::class, 'import'])->name('import');
        Route::get('/{profil}/edit', [ProfilController::class, 'edit'])->name('edit');
        Route::get('/{profil}/ubah', function (Request $request, $profil) {
            return redirect()->route('profils.edit', ['profil' => $profil] + $request->query());
        });
        Route::put('/{profil}', [ProfilController::class, 'update'])->name('update');
        Route::delete('/{profil}', [ProfilController::class, 'destroy'])->name('destroy');
    });

    foreach (['dosen', 'profil-dosen', 'data-profil-dosen'] as $profilAlias) {
        Route::get("/{$profilAlias}", function (Request $request) {
            return redirect()->route('profils.index', $request->query());
        });
    }

    Route::get('/profils', function (Request $request) {
        return redirect()->route('profils.index', $request->query());
    });
    Route::get('/profils/create', function () {
        return redirect()->route('profils.create');
    });
    Route::get('/profils/template/download', function () {
        return redirect()->route('profils.template');
    });
    Route::post('/profils/import', function () {
        abort(410);
    });
    Route::get('/profils/{profil}/edit', function (Request $request, $profil) {
        return redirect()->route('profils.edit', ['profil' => $profil] + $request->query());
    });
    Route::put('/profils/{profil}', function () {
        abort(410);
    });
    Route::delete('/profils/{profil}', function () {
        abort(410);
    });

    Route::resource('kepangkatan', KepangkatanController::class)
        ->names('kepangkatan')
        ->except(['show']);
    Route::get('kepangkatan/{kepangkatan}', function () {
        return redirect()->route('kepangkatan.index');
    })->name('kepangkatan.show.redirect');
    Route::get('kepangkatan/template/download', [KepangkatanController::class, 'downloadTemplate'])
        ->name('kepangkatan.template');
    Route::post('kepangkatan/import', [KepangkatanController::class, 'import'])
        ->name('kepangkatan.import');
    Route::get('kepangkatan/export/pdf', [KepangkatanController::class, 'exportPdf'])
        ->name('kepangkatan.export');

    foreach (['monitoring-kepangkatan', 'data-kepangkatan', 'kepangkatan-dosen'] as $kepangkatanAlias) {
        Route::get("/{$kepangkatanAlias}", [KepangkatanController::class, 'index']);
    }
});
