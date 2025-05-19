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

// Root route and public routes should be OUTSIDE any middleware
Route::get('/', function () {
    return redirect('/login');
});

// Login routes - add guest middleware to prevent redirect loops
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
});

// Keep logout route outside guest middleware as it requires authentication
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public routes - accessible to all
Route::get('/lab-logging', [LabScheduleController::class, 'logging'])->name('lab.logging');
Route::post('/lab-schedule/rfid-attendance', [LabScheduleController::class, 'handleRfidAttendance']);
Route::get('/lab-schedule/logs', [LabScheduleController::class, 'getLabLogs']);
Route::get('/lab-schedule/check-availability/{laboratory}', [LabScheduleController::class, 'checkAvailability']);
Route::get('/lab-schedule/all-labs-status', [LabScheduleController::class, 'getAllLabsStatus']);

// Basic authenticated routes (accessible by all authenticated users)
Route::middleware(['auth'])->group(function () {
    // Profile and notification routes (accessible by all authenticated users)
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/my-tasks', [TaskController::class, 'index'])->name('my.tasks');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');

    // Admin-only routes (group_id = 1)
    Route::middleware([\App\Http\Middleware\CheckRole::class.':1'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        
        // Reports routes - Admin only
        Route::prefix('reports')->group(function () {
            Route::get('/category', [ReportController::class, 'categoryReport'])->name('reports.category');
            Route::get('/location', [ReportController::class, 'locationReport'])->name('reports.location');
            Route::get('/category/{category}/details', [ReportController::class, 'categoryDetails'])->name('reports.category.details');
            Route::get('/location/{location}/details', [ReportController::class, 'locationDetails'])->name('reports.location.details');
            Route::get('/asset-history/{asset}', [ReportController::class, 'assetHistory'])->name('reports.asset-history');
            Route::get('/procurement-history', [ReportController::class, 'procurementHistory'])->name('reports.procurement-history');
            Route::get('/disposal-history', [ReportController::class, 'disposalHistory'])->name('reports.disposal-history');
            Route::get('/asset-history/{asset}/maintenance', [ReportController::class, 'assetMaintenanceHistory'])->name('reports.asset-maintenance-history');
            Route::get('/asset-history/{asset}/repairs', [ReportController::class, 'assetRepairHistory'])->name('reports.asset-repair-history');        });
    });

    // Admin and Staff routes (group_id = 1 or 2)
    Route::middleware([\App\Http\Middleware\CheckRole::class.':1,2'])->group(function () {
        // Asset management routes
        Route::resource('assets', AssetController::class);
        Route::get('/assets/fetch/{serialNumber}', [AssetController::class, 'fetch'])->name('assets.fetch');
        Route::get('/qr-list', [AssetController::class, 'qrList'])->name('qr.list');
        Route::post('/qrcodes/export', [AssetController::class, 'exportQrCodes'])->name('qrcodes.export');
        Route::post('/qrcodes/preview', [AssetController::class, 'previewQrCodes'])->name('qrcodes.preview');
        Route::post('/assets/{asset}/dispose', [AssetController::class, 'dispose'])->name('assets.dispose');
        
        // Maintenance routes
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
        
        Route::get('/lab-schedule/history', [LabScheduleController::class, 'viewHistory'])->name('lab-schedule.history');
        Route::post('/lab-schedule/destroy-multiple', [LabScheduleController::class, 'destroyMultiple'])->name('lab-schedule.destroyMultiple');
        Route::get('/lab-schedule/preview-pdf', [LabScheduleController::class, 'previewPDF'])->name('lab-schedule.previewPDF');
        Route::get('/lab-schedule/export-pdf', [LabScheduleController::class, 'exportPDF'])->name('lab-schedule.exportPDF');
    });
});
