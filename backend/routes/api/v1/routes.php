<?php

use App\Http\Api\Controllers\V1\CurrentUserController;
use App\Http\Api\Controllers\V1\GetPermissionsController;
use App\Http\Api\Controllers\V1\NotificationController;
use App\Http\Api\Controllers\V1\User\UpdateUserAvatarController;
use App\Http\Api\Controllers\V1\User\UpdateUserProfileController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'public',
], base_path('routes/api/v1/public.php'));

Route::group([
    'prefix' => 'auth',
], base_path('routes/api/v1/auth.php'));

Route::group([
    'prefix' => '',
    'middleware' => 'auth:sanctum',
], function () {
    Route::get('/user', CurrentUserController::class);
    Route::patch('/user/profile', UpdateUserProfileController::class);
    Route::patch('/user/change-avatar', UpdateUserAvatarController::class);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/permissions', GetPermissionsController::class);
});

Route::group([
    'prefix' => 'user',
    'middleware' => 'auth:sanctum',
], base_path('routes/api/v1/user.php'));

Route::group([
    'prefix' => 'orders',
    'middleware' => 'auth:sanctum',
], base_path('routes/api/v1/orders.php'));

Route::group([
    'prefix' => 'teams',
], base_path('routes/api/v1/teams.php'));

Route::group([
    'prefix' => 'media',
    'middleware' => 'auth:sanctum',
], base_path('routes/api/v1/media.php'));

Route::group([
    'prefix' => 'todo',
    'middleware' => 'auth:sanctum',
], base_path('routes/api/v1/todo.php'));

Route::group([
    'prefix' => 'chats',
    'middleware' => 'auth:sanctum',
], base_path('routes/api/v1/chats.php'));

Route::group([
    'prefix' => 'addressbooks',
    'middleware' => 'auth:sanctum',
], base_path('routes/api/v1/addressbook.php'));
