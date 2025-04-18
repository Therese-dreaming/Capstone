<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
// Inside the middleware group
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    Route::resource('users', UserController::class);
    Route::get('/qr-list', [AssetController::class, 'qrList'])->name('qr.list');
    Route::post('/qrcodes/export', [AssetController::class, 'exportQrCodes'])->name('qrcodes.export');
    Route::post('/qrcodes/preview', [AssetController::class, 'previewQrCodes'])->name('qrcodes.preview');
    Route::resource('categories', CategoryController::class);


    // Add these routes for asset management
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    Route::post('/assets/{asset}/dispose', [AssetController::class, 'dispose'])->name('assets.dispose');

    // Maintenance Routes
    Route::get('/maintenance/schedule', [MaintenanceController::class, 'schedule'])->name('maintenance.schedule');
    Route::post('/maintenance/store', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/upcoming', [MaintenanceController::class, 'upcoming'])->name('maintenance.upcoming');
    Route::get('/maintenance/history', [MaintenanceController::class, 'history'])->name('maintenance.history');
    Route::patch('/maintenance/{id}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::get('/maintenance/lab/{labNumber}/edit', [MaintenanceController::class, 'editLab'])->name('maintenance.editLab');
    Route::patch('/maintenance/lab/{labNumber}', [MaintenanceController::class, 'updateLab'])->name('maintenance.updateLab');
    Route::get('/maintenance/{lab}/date/{date}/edit', [MaintenanceController::class, 'editByDate'])->name('maintenance.editByDate');
    Route::patch('/maintenance/{lab}/date/{date}', [MaintenanceController::class, 'updateByDate'])->name('maintenance.updateByDate');
    Route::post('/maintenance/{lab}/date/{date}/task', [MaintenanceController::class, 'addTask'])->name('maintenance.addTask');
    Route::post('/maintenance/add-task', [MaintenanceController::class, 'addNewTask'])->name('maintenance.addNewTask');
    Route::delete('/maintenance/task/{maintenance}', [MaintenanceController::class, 'deleteTask'])->name('maintenance.deleteTask');
    Route::get('/maintenance/preview-pdf', [MaintenanceController::class, 'previewPDF'])->name('maintenance.previewPDF');
    Route::get('/maintenance/export-pdf', [MaintenanceController::class, 'exportPDF'])->name('maintenance.exportPDF');
    Route::get('/maintenance/export/excel', [MaintenanceController::class, 'exportExcel'])->name('maintenance.exportExcel');
    Route::patch('/maintenance/{lab}/date/{date}/complete-all', [MaintenanceController::class, 'completeAllByDate'])->name('maintenance.completeAllByDate');
    Route::delete('/maintenance/{lab}/date/{date}/cancel-all', [MaintenanceController::class, 'cancelAllByDate'])
        ->name('maintenance.cancelAllByDate');

    // Repair Request Routes
    Route::get('/repair/request', [RepairRequestController::class, 'create'])->name('repair.request');
    Route::post('/repair/store', [RepairRequestController::class, 'store'])->name('repair.store');
    Route::get('/repair-status', [RepairRequestController::class, 'status'])->name('repair.status');
    Route::get('/repair-completed', [RepairRequestController::class, 'completed'])->name('repair.completed');
    Route::put('/repair-requests/{id}', [RepairRequestController::class, 'update'])->name('repair-requests.update');
    Route::delete('/repair-requests/delete/{id}', [RepairRequestController::class, 'destroy'])->name('repair-requests.destroy');
    Route::get('/repair-requests/urgent', [RepairRequestController::class, 'urgent'])->name('repair.urgent');
    
    // Add these new routes for repair exports
    Route::get('/repair-completed/preview-pdf', [RepairRequestController::class, 'previewPDF'])->name('repair.previewPDF');
    Route::get('/repair-completed/export-pdf', [RepairRequestController::class, 'exportPDF'])->name('repair.exportPDF');
    Route::get('/repair-completed/export-excel', [RepairRequestController::class, 'exportExcel'])->name('repair.exportExcel');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Reports Routes
    Route::get('/reports/category', [ReportController::class, 'categoryReport'])->name('reports.category');
    Route::get('/reports/category/{category}/details', [ReportController::class, 'categoryDetails'])->name('reports.category.details');
    Route::get('/reports/location', [ReportController::class, 'locationReport'])->name('reports.location');
    Route::get('/reports/location/{location}/details', [ReportController::class, 'locationDetails'])->name('reports.location.details');
    Route::get('/reports/asset-history/{asset}', [ReportController::class, 'assetHistory'])
        ->name('reports.asset-history')
        ->middleware(['auth']);
    Route::get('/reports/procurement-history', [ReportController::class, 'procurementHistory'])->name('reports.procurement-history');
    Route::get('/reports/disposal-history', [ReportController::class, 'disposalHistory'])->name('reports.disposal-history');
});
