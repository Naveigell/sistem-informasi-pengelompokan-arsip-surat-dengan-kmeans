<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
Route::post('/calculate', [\App\Http\Controllers\KmeansController::class, 'calculate'])->name('calculate');
Route::post('/upload', [\App\Http\Controllers\UploadController::class, 'upload'])->name('upload');
