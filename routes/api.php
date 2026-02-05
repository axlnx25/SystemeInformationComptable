<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\OperationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Journal routes
    Route::apiResource('journals', JournalController::class);
    Route::get('journals/{journal}/operations', [JournalController::class, 'getOperations']);
    Route::get('journals/{journal}/totals', [JournalController::class, 'getTotals']);
    Route::get('journals/{journal}/validate-balance', [JournalController::class, 'validateBalance']);
    Route::get('journals/{journal}/next-operation-number', [OperationController::class, 'getNextOperationNumber']);

    // Operation routes
    Route::apiResource('operations', OperationController::class);
    Route::post('operations/batch', [OperationController::class, 'storeBatch']);
    Route::get('operations/by-number/{numero_operation}', [OperationController::class, 'getByOperationNumber']);
    Route::get('operations/validate/{numero_operation}', [OperationController::class, 'validateOperationBalance']);
});
