<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;

Route::get('/admin/{route_segment1?}/{route_segment2?}/{route_segment3?}', function () {
    return view('angular');
});

//Unauthenticated routes

Route::prefix('web-app')->name('admin.')->group(function () {
    Route::get('csrf-token', [AuthController::class, 'csrfToken'])->name('get.csrf-token');
    Route::post('login', [AuthController::class, 'login'])->name('get.login');
    Route::post('register', [AuthController::class, 'register'])->name('get.register');
    Route::post('forgot-password', [AuthController::class, 'postForgotPassword'])->name('post.forgot-password');
    Route::post('reset-password', [AuthController::class, 'postResetPassword'])->name('post.reset-password');
    Route::get('users/export-user', [UserController::class, 'exportUser'])->name('get.users.export-data');
});

//Authenticated routes

Route::middleware('auth:sanctum')->prefix('web-app')->name('admin.')->group(function () {
    //Route::get('me', [AuthController::class, 'me'])->name('get.me');
    Route::post('logout', [AuthController::class, 'logout'])->name('post.logout');
    Route::post('logout-all', [AuthController::class, 'logoutAll'])->name('post.logout-all');
    Route::post('logout-by-token-id', [AuthController::class, 'logoutAll'])->name('post.logout-by-token-id');
    Route::post('users/create-update', [UserController::class, 'createOrUpdate'])->name('post.users.create-update');
    Route::post('users/delete', [UserController::class, 'destroy'])->name('post.users.delete');
    Route::post('users/mutiple-delete', [UserController::class, 'multipleDelete'])->name('post.users.multiple-delete');
    Route::get('users/change-status/{user_id}', [UserController::class, 'changeStatus'])->name('get.users.change-status');
    Route::post('users/multiple-change-status', [UserController::class, 'multipleChangeStatus'])->name('post.users.multiple-change-status');
    Route::get('users/me', [UserController::class, 'currentUserDetail'])->name('get.users.me');
    Route::post('users/update-profile', [UserController::class, 'updateProfile'])->name('post.users.update-profile');
    Route::resource('users', UserController::class, ['names' => 'users', 'except' => ['destroy']]);
});
