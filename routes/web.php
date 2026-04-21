<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['laravel' => app()->version()];
});
Route::get('/sanctum/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});



require __DIR__ . '/auth.php';
