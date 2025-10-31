<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KepangkatanController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('profils.index')
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.attempt');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::resource('profils', ProfilController::class)
        ->names('profils')
        ->except(['show']);

    Route::resource('kepangkatan', KepangkatanController::class)
        ->names('kepangkatan')
        ->except(['show']);
});
