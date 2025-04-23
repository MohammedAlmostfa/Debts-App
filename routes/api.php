<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Auth\AuthController;

Route::post('/login', [AuthController::class, 'login']); // Handles user login
// User logout
Route::post('logout', [AuthController::class, 'logout']); // Logs out the authenticated user
// Refresh JWT token
Route::post('refresh', [AuthController::class, 'refresh']); // Refreshes the JWT token

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('customers', CustomerController::class);
