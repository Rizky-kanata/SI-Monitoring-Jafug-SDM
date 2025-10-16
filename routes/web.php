<?php

use App\Http\Controllers\KepangkatanController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/profils');

Route::resource('profils', ProfilController::class)
    ->names('profils')
    ->except(['show']);

Route::resource('kepangkatan', KepangkatanController::class)
    ->names('kepangkatan')
    ->except(['show']);
