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
use App\Http\Controllers\NonRegisteredAssetController;
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

// Add the fetch route here, outside of any middleware
Route::get('/assets/fetch/{serialNumber}', [AssetController::class, 'fetch'])
    ->name('assets.fetch');

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

    // Repair Request (GET and POST) for all authenticated users
    Route::get('/repair-request', [RepairRequestController::class, 'create'])->name('repair.request');
    Route::post('/repair-request', [RepairRequestController::class, 'store'])->name('repair.request.store');

    // Repair Calls route
    Route::get('/repair-calls', [RepairRequestController::class, 'calls'])->name('repair.calls');
    Route::post('/repair-calls/{id}/evaluate', [RepairRequestController::class, 'evaluate'])->name('repair.calls.evaluate');

    // Repair Status route
    Route::get('/repair-status', [RepairRequestController::class, 'status'])->name('repair.status');

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
            Route::get('/asset-history/{asset}/repairs', [ReportController::class, 'assetRepairHistory'])->name('reports.asset-repair-history');
            Route::get('/lab-usage', [ReportController::class, 'labUsage'])->name('reports.lab-usage');
            Route::get('/lab-usage/export', [ReportController::class, 'exportLabUsageToPdf'])->name('reports.lab-usage.export');
        });
    });

    // Admin and Staff routes (group_id = 1 or 2)
    Route::middleware([\App\Http\Middleware\CheckRole::class.':1,2'])->group(function () {
        // Authentication check route
        Route::get('/check-auth', function () {
            return response()->json(['authenticated' => auth()->check()]);
        })->name('check.auth');

        // Employee Performance Report - Accessible to both admin and staff
        Route::get('/reports/employee-performance', [ReportController::class, 'employeePerformance'])->name('reports.employee-performance');

        // Asset management routes
        Route::resource('assets', AssetController::class);
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
        Route::post('/maintenance/destroy-multiple', [MaintenanceController::class, 'destroyMultiple'])->name('maintenance.destroyMultiple');
        Route::get('/maintenance/get-lab-assets/{labNumber}', [MaintenanceController::class, 'getLabAssets'])->name('maintenance.getLabAssets');

        // Repair Request Routes
        Route::middleware([\App\Http\Middleware\CheckRole::class.':1,3'])->group(function () {
            Route::get('/repair-requests', [RepairRequestController::class, 'index'])->name('repair.index');
        });
        Route::get('/repair-requests/completed', [RepairRequestController::class, 'completed'])->name('repair.completed');
        Route::get('/repair-requests/{id}', [RepairRequestController::class, 'show'])->name('repair.show');
        Route::get('/repair-requests/{id}/data', [RepairRequestController::class, 'getData'])->name('repair.data');
        Route::get('/repair-completed', [RepairRequestController::class, 'completed'])->name('repair.completed');
        Route::delete('/repair-completed/{id}', [RepairRequestController::class, 'destroy'])->name('repair-requests.destroy');
        Route::put('/repair-requests/{id}', [RepairRequestController::class, 'update'])->name('repair-requests.update');
        Route::resource('repair-requests', App\Http\Controllers\RepairRequestController::class)->only(['update', 'destroy']);
        Route::get('/repair-requests/delete/{id}', [App\Http\Controllers\RepairRequestController::class, 'destroy'])
            ->name('repair-requests.delete')
            ->where('id', '[0-9]+');
        Route::post('/repair-requests/destroy-multiple', [RepairRequestController::class, 'destroyMultiple'])->name('repair.destroyMultiple');

        // Repair exports
        Route::get('/repair-completed/preview-pdf', [RepairRequestController::class, 'previewPDF'])->name('repair.previewPDF');
        Route::get('/repair-completed/export-pdf', [RepairRequestController::class, 'exportPDF'])->name('repair.exportPDF');
        Route::get('/repair-completed/export-excel', [RepairRequestController::class, 'exportExcel'])->name('repair.exportExcel');
        Route::get('/repair/details/{id}', [RepairRequestController::class, 'details'])->name('repair.details');
        Route::get('/repair-requests/{id}/completion-form', [RepairRequestController::class, 'showCompletionForm'])->name('repair.completion-form');

        // Lab Schedule Routes
        Route::get('/lab-schedule/history', [LabScheduleController::class, 'history'])->name('lab-schedule.history');
        Route::post('/lab-schedule/destroy-multiple', [LabScheduleController::class, 'destroyMultiple'])->name('lab-schedule.destroyMultiple');
        Route::get('/lab-schedule/preview-pdf', [LabScheduleController::class, 'previewPDF'])->name('lab-schedule.previewPDF');
        Route::get('/lab-schedule/export-pdf', [LabScheduleController::class, 'exportPDF'])->name('lab-schedule.exportPDF');

        // History routes
        Route::get('/actions-history', [DashboardController::class, 'userActionsHistory'])->name('user.actions.history');
        Route::get('/repairs-history', [App\Http\Controllers\DashboardController::class, 'allRepairsHistory'])->name('repairs.history');
        Route::get('/maintenance-history', [App\Http\Controllers\DashboardController::class, 'allMaintenanceHistory'])->name('user.maintenance.history');

        // Scanner route
        Route::get('/scanner', function () {
            return view('scanner');
        })->name('scanner');
    });

    // Secretary Dashboard route (group_id = 2)
    Route::get('/secretary-dashboard', [DashboardController::class, 'secretaryDashboard'])
        ->middleware(['auth', \App\Http\Middleware\CheckRole::class.':2'])
        ->name('secretary-dashboard');

    // Non-Registered Assets Routes
    Route::get('/non-registered-assets', [NonRegisteredAssetController::class, 'index'])->name('non-registered-assets.index');
    Route::post('/non-registered-assets', [NonRegisteredAssetController::class, 'store'])->name('non-registered-assets.store');
    Route::put('/non-registered-assets/{id}', [NonRegisteredAssetController::class, 'update'])->name('non-registered-assets.update');
});
