<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Main Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Feedback Page Route
Route::get('/feedback', [DashboardController::class, 'feedback'])->name('feedback');