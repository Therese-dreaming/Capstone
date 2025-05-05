<?php

use App\Http\Controllers\MaintenanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/categories/{category}/assets', [MaintenanceController::class, 'getAssetsByCategory']);
