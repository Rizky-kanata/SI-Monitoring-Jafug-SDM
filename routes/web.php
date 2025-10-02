<?php

use App\Http\Controllers\DosenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-api', [DosenController::class, 'testapi']);
Route::get('/dosens', [DosenController::class, 'index']);
Route::post('/dosens', [DosenController::class, 'store']);