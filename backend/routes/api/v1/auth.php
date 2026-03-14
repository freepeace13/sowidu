<?php

use App\Http\Api\Controllers\V1\Auth\LoginController;
use App\Http\Api\Controllers\V1\Auth\SwitchAccountController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/switch', SwitchAccountController::class);
});
// Route::post('register', RegisterController::class);
// Route::post('forgot-password', RegisterController::class);
