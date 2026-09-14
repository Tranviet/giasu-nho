<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ChildDashboardController;
use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

// 1. Homepage / Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Web Authentication Routes (Parent Portal)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Child Dashboard / Home Screen
Route::get('/child-dashboard', [ChildDashboardController::class, 'index'])->name('child.dashboard');
