<?php

use Illuminate\Http\Request;
use App\Models\CustomerDebts;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDebtsController;
use App\Http\Controllers\FinancialReportController;

Route::post('/login', [AuthController::class, 'login']); // Handles user login

Route::post('resetPassword', [AuthController::class, 'resetPassword']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('customer', CustomerController::class);
Route::apiResource('debt', DebtController::class);
Route::apiResource('customerdebt', CustomerDebtsController::class);

Route::apiResource('receipt', ReceiptController::class);
Route::get('receipt/{receipt}/receiptItem', [ReceiptController::class,'getReceiptItems']);
Route::apiResource('store', StoreController::class);
Route::get('/financialReport', [FinancialReportController::class, 'index']);
