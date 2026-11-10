<?php

use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticationController::class, 'dashboard'])->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticationController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthenticationController::class, 'storeLogin'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [AuthenticationController::class, 'createRegistration'])->name('register');
    Route::post('/register', [AuthenticationController::class, 'storeRegistration'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});

Route::post('/logout', [AuthenticationController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function (): void {
    Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
});

Route::middleware(['auth', 'role:manager'])->group(function (): void {
    Route::view('/manager', 'manager.dashboard')->name('manager.dashboard');
});

Route::middleware(['auth', 'role:employee'])->group(function (): void {
    Route::view('/employee', 'employee.dashboard')->name('employee.dashboard');
});
