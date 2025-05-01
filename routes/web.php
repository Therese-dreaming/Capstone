<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LabScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->intended('/assets');
    }
    return redirect('/login');
});

// Add this route before the login POST route
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
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
    Route::get('/assets/fetch/{serial_number}', [AssetController::class, 'fetchBySerial'])->name('assets.fetch');

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
    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::post('/maintenance/destroy-multiple', [MaintenanceController::class, 'destroyMultiple'])->name('maintenance.destroyMultiple');    
    
    // Repair Request Routes
    Route::get('/repair/request', [RepairRequestController::class, 'create'])->name('repair.request');
    Route::post('/repair/store', [RepairRequestController::class, 'store'])->name('repair.store');
    Route::get('/repair-status', [RepairRequestController::class, 'status'])->name('repair.status');
    Route::get('/repair-completed', [RepairRequestController::class, 'completed'])->name('repair.completed');
    Route::delete('/repair-completed/{id}', [RepairRequestController::class, 'destroy'])->name('repair-requests.destroy');
    Route::put('/repair-requests/{id}', [RepairRequestController::class, 'update'])->name('repair-requests.update');
    Route::delete('/repair-requests/delete/{id}', [RepairRequestController::class, 'destroy'])->name('repair-requests.destroy');
    Route::post('/repair-requests/destroy-multiple', [RepairRequestController::class, 'destroyMultiple'])->name('repair.destroyMultiple');
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
    Route::get('/reports/asset-history/{asset}/maintenance', [ReportController::class, 'assetMaintenanceHistory'])
        ->name('reports.asset-maintenance-history');
    Route::get('/reports/asset-history/{asset}/repairs', [ReportController::class, 'assetRepairHistory'])
        ->name('reports.asset-repair-history');

    Route::get('/maintenance/get-lab-assets/{labNumber}', [MaintenanceController::class, 'getLabAssets'])->name('maintenance.getLabAssets');
   
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Replace the role middleware route with this
Route::middleware(['auth'])->group(function () {
    Route::get('/my-tasks', [TaskController::class, 'index'])->name('my.tasks');
    
    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    
    // Lab Schedule Routes
    // Add these routes within your auth middleware group
    Route::get('/lab-schedule', [LabScheduleController::class, 'index'])->name('lab.schedule');
    Route::post('/lab-schedule', [LabScheduleController::class, 'store']);
    Route::get('/lab-schedule/events', [LabScheduleController::class, 'getEvents']);
    Route::get('/lab-logging', [LabScheduleController::class, 'logging'])->name('lab.logging');
    Route::post('/lab-logging/get-schedule', [LabScheduleController::class, 'getScheduleByRfid'])->name('lab.logging.schedule');
    Route::post('/lab-logging/submit', [LabScheduleController::class, 'submitLog'])->name('lab.logging.submit');
    Route::get('/lab-history', [LabScheduleController::class, 'history'])->name('lab.history');
    Route::post('/lab-schedule/delete', [LabScheduleController::class, 'destroy'])->name('lab.delete');
});
