<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest routes (authentication)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Journals
    Route::get('/journals/new', [JournalController::class, 'newJournal'])->name('journals.new');
    Route::resource('journals', JournalController::class);
    Route::get('/journals/{journal}/operations', [JournalController::class, 'operations'])->name('journals.operations');
    Route::post('/journals/{journal}/operations', [OperationController::class, 'storeBatch'])->name('operations.storeBatch');
    Route::post('/journals/save', [JournalController::class, 'saveJournalWithOperations'])->name('journals.saveWithOperations');
    Route::get('/journals/{journal}/history', [JournalController::class, 'history'])->name('journals.history');
    Route::delete('/journals/{journal}/operations/{numeroOperation}', [OperationController::class, 'destroy'])->name('operations.destroy');
    
    // User management (admin only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
