<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RedirectController;

Route::get('/', [RedirectController::class, 'redirectToHome'])->name('home');

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'do_login'])->name('do_login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas de admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    require __DIR__.'/admin/routes.php';
});

// Rutas de cliente
Route::prefix('client')->middleware(['auth', 'client'])->group(function () {
    require __DIR__.'/client/routes.php';
});