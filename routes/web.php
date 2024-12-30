<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('loginForm');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('registerForm');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
// частично обновить данные методом patch
Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('updateProfile');

Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('changePasswordForm');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');

