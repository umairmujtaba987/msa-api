<?php

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
| All routes are prefixed with /api/v1 by this file + Laravel's api prefix.
| Set NUXT_PUBLIC_API_BASE_URL (or default in nuxt.config) to .../api/v1
*/
Route::prefix('')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::get('/me', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::middleware('role:superadmin')->group(function (): void {
            Route::apiResource('roles', RoleController::class);
        });

        Route::middleware('role:superadmin|admin_manager')->group(function (): void {
            Route::apiResource('users', UserController::class);
        });

        Route::get('/settings', [SettingController::class, 'index']);
        Route::put('/settings', [SettingController::class, 'update']);

        Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);

        Route::apiResource('bookings', BookingController::class);
        Route::get('/booking-config', [BookingController::class, 'config']);
        Route::post('/bookings/calculate-price', [BookingController::class, 'calculatePrice']);
        Route::get('/reports/bookings/export', [ReportController::class, 'exportBookings']);
        Route::patch('/bookings/{booking}/paid', [BookingController::class, 'markPaid']);
        Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
        Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    });
});
