<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    // Add these inside your auth middleware group
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.list');
    // Add these routes in your auth middleware group
    Route::resource('groups', GroupController::class);
    Route::resource('users', UserController::class);
    Route::get('/qr-list', [AssetController::class, 'qrList'])->name('qr.list');
    Route::post('/qrcodes/export', [AssetController::class, 'exportQrCodes'])->name('qrcodes.export');
    Route::post('/qrcodes/preview', [AssetController::class, 'previewQrCodes'])->name('qrcodes.preview');
    Route::resource('categories', CategoryController::class);
});
