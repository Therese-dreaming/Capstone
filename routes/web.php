<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    // Add these routes in your auth middleware group
    // Replace the full resource route with specific routes
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    Route::resource('users', UserController::class);
    Route::get('/qr-list', [AssetController::class, 'qrList'])->name('qr.list');
    Route::post('/qrcodes/export', [AssetController::class, 'exportQrCodes'])->name('qrcodes.export');
    Route::post('/qrcodes/preview', [AssetController::class, 'previewQrCodes'])->name('qrcodes.preview');
    Route::resource('categories', CategoryController::class);
    
    // Maintenance routes
    Route::get('/maintenance/schedule', [MaintenanceController::class, 'scheduleForm'])->name('maintenance.schedule');
    Route::post('/maintenance/schedule', [MaintenanceController::class, 'scheduleStore'])->name('maintenance.schedule.store');
    Route::get('/categories/{category}/assets', [MaintenanceController::class, 'getAssetsByCategory']);
    Route::get('/assets/{asset}', [MaintenanceController::class, 'getAssetDetails']);
    Route::get('/maintenance/schedules/{date}', [MaintenanceController::class, 'getSchedulesByDate']);
    Route::get('/maintenance/schedules/month/{start}/{end}', [MaintenanceController::class, 'getSchedulesByDateRange']);
    Route::get('/maintenance/upcoming', [MaintenanceController::class, 'getUpcomingMaintenance']);
    Route::get('/maintenance/upcoming-view', [MaintenanceController::class, 'upcomingView'])->name('maintenance.upcoming');
    Route::get('/maintenance/completed', [MaintenanceController::class, 'getCompletedMaintenance']);
    Route::get('/maintenance/history', [MaintenanceController::class, 'history'])->name('maintenance.history');
    Route::get('/maintenance/{id}/details', [MaintenanceController::class, 'getDetails'])->name('maintenance.details');
    Route::get('/maintenance/technicians', [MaintenanceController::class, 'getTechnicians'])->name('maintenance.technicians');
    Route::post('/maintenance/{id}/complete', [MaintenanceController::class, 'completeMaintenance'])->name('maintenance.complete');
    Route::put('/maintenance/{id}', [MaintenanceController::class, 'update'])->name('maintenance.update');
    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::get('/maintenance/{id}/edit', [MaintenanceController::class, 'edit'])->name('maintenance.edit');

    // Repair Routes
    Route::get('/repair/request', [RepairRequestController::class, 'create'])->name('repair.request');
    Route::post('/repair/store', [RepairRequestController::class, 'store'])->name('repair.store');
    Route::get('/repair-status', [RepairRequestController::class, 'status'])->name('repair.status');
    Route::get('/repair-completed', [RepairRequestController::class, 'completed'])->name('repair.completed');
    Route::put('/repair-requests/{id}', [RepairRequestController::class, 'update'])->name('repair-requests.update');
    Route::delete('/repair-requests/delete/{id}', [RepairRequestController::class, 'destroy'])->name('repair-requests.destroy');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});
