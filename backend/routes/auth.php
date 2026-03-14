<?php

use App\Http\Controllers\Inertia\Auth\ForgotPasswordController;
use App\Http\Controllers\Inertia\Auth\LoginController;
use App\Http\Controllers\Inertia\Auth\RegisterController;
use App\Http\Controllers\Inertia\Auth\ResetPasswordController;
use App\Http\Controllers\Inertia\Auth\VerificationController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => AuthMiddleware::class,
], function () {
    Route::middleware(['guest'])->group(function () {
        Route::controller(LoginController::class)->group(function () {
            Route::get('/', 'create')->name('home');
            Route::get('/login', 'create')->name('auth.login');
            Route::post('/login', 'store')->name('auth.login.store');
            Route::get('/authorize/{accessToken}', 'authorize')->name('auth.authorize');
        });

        Route::controller(RegisterController::class)->group(function () {
            Route::get('/register', 'create')->name('auth.register');
            Route::post('/register', 'store')->name('auth.register.store');
        });

        Route::controller(ForgotPasswordController::class)->group(function () {
            Route::get('/password/reset', 'showLinkRequestForm')
                ->name('auth.password.request');
            Route::post('/password/email', 'sendResetLinkEmail')
                ->name('auth.password.email');
        });

        Route::controller(ResetPasswordController::class)->group(function () {
            Route::get('/password/reset/{token}', 'showResetForm')
                ->name('auth.password.reset');

            Route::post('/password/reset', 'reset')
                ->name('auth.password.update');
        });

        Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
            ->name('auth.verification.verify')
            ->middleware('guest');

        Route::post('/email/verify/resend', [VerificationController::class, 'resend'])
            ->name('auth.verification.resend')
            ->middleware('guest');
    });
});

Route::delete('/logout', [LoginController::class, 'destroy'])
    ->name('auth.logout');
