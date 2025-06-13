<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DownloadController;

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'do_login'])->name('do_login');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');

    //USUARIOS
    Route::get('admin/users/', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('admin/users/', [UserController::class, 'do_create'])->name('admin.users.do_create');
    Route::get('admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('admin/users/{id}', [UserController::class, 'do_edit'])->name('admin.users.do_edit');
    Route::patch('admin/users/{id}/toggle', [UserController::class, 'do_active'])->name('admin.users.do_active');

    //DESCAGAS
    Route::get('admin/downloads', [DownloadController::class, 'index'])->name('admin.downloads.index');
    Route::post('admin/downloads', [DownloadController::class, 'do_create'])->name('admin.downloads.do_create');
    Route::get('admin/downloads/{id}', [DownloadController::class, 'show'])->name('admin.downloads.show');
    Route::get('/downloads/status', [DownloadController::class, 'status'])->name('admin.downloads.status');
    
    Route::get('/', function () {
        return redirect()->route('home.index');
    });
});
