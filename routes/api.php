<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ReceiptController;

Route::post('/login', [AuthController::class, 'login']); // Handles user login

Route::post('logout', [AuthController::class, 'resetPassword']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('customer', CustomerController::class);
Route::apiResource('debt', DebtController::class);
Route::apiResource('receipt', ReceiptController::class);
Route::get('receipt/{receipt}/receiptItem', [ReceiptController::class,'getReceiptItems']);
