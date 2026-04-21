<?php

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/me', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Superadmin only
    Route::middleware('role:superadmin')->group(function () {
        Route::apiResource('roles', RoleController::class);
    });

    // Admin + Superadmin
    Route::middleware('role:superadmin|admin_manager')->group(function () {
        Route::apiResource('users', UserController::class);
    });
    
    // Settings (Admin / System config)
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index']);
    Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update']);

    // Profile management
    Route::get('/profile', [\App\Http\Controllers\Auth\AuthController::class, 'profile']);
    Route::put('/profile', [\App\Http\Controllers\Auth\AuthController::class, 'updateProfile']);
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);

    // Bookings and Dashboard (Available to authenticated users - roles are generally filtered in components but could be refined here)
    Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'getStats']);
    
    Route::apiResource('bookings', \App\Http\Controllers\BookingController::class);
    Route::get('/booking-config', [\App\Http\Controllers\BookingController::class, 'config']);
    Route::post('/bookings/calculate-price', [\App\Http\Controllers\BookingController::class, 'calculatePrice']);
    Route::get('/reports/bookings/export', [ReportController::class, 'exportBookings']);
    Route::patch('/bookings/{booking}/paid', [\App\Http\Controllers\BookingController::class, 'markPaid']);
    Route::patch('/bookings/{booking}/confirm', [\App\Http\Controllers\BookingController::class, 'confirm']);
    Route::patch('/bookings/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel']);
});

require __DIR__ . '/auth.php';


