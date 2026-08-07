<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// ====================== GUEST ======================
Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'create')->name('login');
        Route::post('/login', 'store')->middleware('throttle:6,1');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'create')->name('register');
        Route::post('/register', 'store')->middleware('throttle:6,1');
    });

    Route::controller(PasswordResetController::class)->group(function () {
        Route::get('/forgot-password', 'request')->name('password.request');
        Route::post('/forgot-password', 'email')->name('password.email')->middleware('throttle:6,1');
        Route::get('/reset-password/{token}', 'reset')->name('password.reset');
        Route::post('/reset-password', 'update')->name('password.store')->middleware('throttle:6,1');
    });
});

// ====================== AUTHENTICATED ======================
Route::middleware('auth')->group(function () {
    Route::controller(EmailVerificationController::class)->group(function () {
        Route::get('/verify-email', 'notice')->name('verification.notice');
        Route::get('/verify-email/{id}/{hash}', 'verify')
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
        Route::post('/email/verification-notification', 'send')
            ->middleware('throttle:6,1')
            ->name('verification.send');
    });
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
