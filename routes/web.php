<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
// jika nak juga route /dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);
