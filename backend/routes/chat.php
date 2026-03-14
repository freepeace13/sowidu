<?php

use App\Enums\Permissions;
use App\Http\Controllers\Inertia\Chat\ConversationController;
use App\Http\Controllers\Inertia\Chat\MessageController;
use App\Http\Controllers\Inertia\Chat\SearchController;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'apps',
    'middleware' => [
        'auth',
        'permission:' . Permissions::CAN_ACCESS_CHAT,
        HandleInertiaRequests::class,
    ],
], function () {
    Route::prefix('chat')
        ->name('chat.')
        ->group(function () {
            Route::get('search', [SearchController::class, 'index'])
                ->name('search');

            Route::controller(MessageController::class)
                ->prefix('{id}/messages')
                ->name('messages.')
                ->group(function () {
                    Route::get('', 'index')->name('index');
                    Route::post('', 'store')->name('store');
                    Route::patch('read', 'read')->name('read');
                    Route::delete('{message_id}', 'destroy')->name('destroy');
                    Route::patch('{message_id}', 'patch')->name('patch');
                });
        });

    Route::apiResource('chat', ConversationController::class)
        ->parameters([
            'chat' => 'id',
        ])
        ->except('update');
});
