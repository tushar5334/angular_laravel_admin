<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\Auth\LoginController;
use App\Http\Controllers\Front\Auth\RegisterController;
use App\Http\Controllers\Front\Auth\ForgotPasswordController;
use App\Http\Controllers\Front\Auth\ResetPasswordController;
use App\Http\Controllers\Front\DashboardController;

/*
|--------------------------------------------------------------------------
| Front Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for admin application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

// Front Open Routes
Route::name('front.')->prefix('/')->group(function () {

    Route::get('login', [LoginController::class, 'showLoginForm'])->name('get.login');
    Route::post('login', [LoginController::class, 'postLogin'])->name('post.login');
    Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('get.forgot-password');
    Route::post('forgot-password', [ForgotPasswordController::class, 'postForgotPassword'])->name('post.forgot-password');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])->name('get.reset-password');
    Route::post('reset-password/{token}', [ResetPasswordController::class, 'postResetPassword'])->name('post.reset-password');
    Route::get('register', [RegisterController::class, 'showSignupForm'])->name('get.register');
    Route::post('register', [RegisterController::class, 'processSignup'])->name('post.register');
});

// Admin Authenticated Routes
Route::middleware(['auth'])->name('front.')->prefix('/')->group(function () {
    Route::get('', [DashboardController::class, 'displayDashboard'])->name('get.dashboard');
    Route::post('logout', [LoginController::class, 'frontPostLogout'])->name('post.logout');
});
