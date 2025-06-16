<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DownloadController;

Route::get('/home', [HomeController::class, 'index'])->name('admin.home.index');

// Usuarios
Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
Route::post('/users', [UserController::class, 'do_create'])->name('admin.users.do_create');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
Route::put('/users/{id}', [UserController::class, 'do_edit'])->name('admin.users.do_edit');
Route::patch('/users/{id}/toggle', [UserController::class, 'do_active'])->name('admin.users.do_active');

// Descargas
Route::get('/downloads', [DownloadController::class, 'index'])->name('admin.downloads.index');
Route::post('/downloads', [DownloadController::class, 'do_create'])->name('admin.downloads.do_create');

Route::get('/downloads/status', [DownloadController::class, 'status'])->name('admin.downloads.status');
Route::get('/downloads/{id}', [DownloadController::class, 'show'])->name('admin.downloads.show');
Route::get('/downloads/{id}/download', [DownloadController::class, 'download'])->name('admin.downloads.download');