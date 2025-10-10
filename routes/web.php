<?php

use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/profils', [ProfilController::class, 'index'])->name('profils.index');
Route::post('/profils', [ProfilController::class, 'store'])->name('profils.store');
