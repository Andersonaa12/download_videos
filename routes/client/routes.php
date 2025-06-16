<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\DownloadController;

Route::get('/home', [HomeController::class, 'index'])->name('client.home.index');

// Descargas
Route::get('/downloads', [DownloadController::class, 'index'])->name('client.downloads.index');
Route::post('/downloads', [DownloadController::class, 'do_create'])->name('client.downloads.do_create');

Route::get('/downloads/status', [DownloadController::class, 'status'])->name('client.downloads.status');
Route::get('/downloads/{id}', [DownloadController::class, 'show'])->name('client.downloads.show');
Route::get('/downloads/{id}/download', [DownloadController::class, 'download'])->name('client.downloads.download');