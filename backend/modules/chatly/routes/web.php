<?php

use App\Enums\Permissions;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Modules\Chatly\Http\Controllers\Inertia\ConversationController;
use Modules\Chatly\Http\Controllers\Inertia\MessageController;
use Modules\Chatly\Http\Controllers\Inertia\SearchController;

Route::middleware([
    'auth',
    'permission:' . Permissions::CAN_ACCESS_CHAT,
    HandleInertiaRequests::class,
])->group(function () {
    Route::get('search', [SearchController::class, 'index'])
        ->name('chatly.search');

    Route::get('/{conversation}/messages', [MessageController::class, 'index'])
        ->name('chatly.messages.show');

    Route::post('/{conversation}/messages', [MessageController::class, 'store'])
        ->name('chatly.messages.store');

    Route::patch('/{conversation}/messages/read', [MessageController::class, 'read'])
        ->name('chatly.messages.read');

    Route::delete('/{conversation}/messages/{message}', [MessageController::class, 'destroy'])
        ->name('chatly.messages.destroy');

    Route::patch('/{conversation}/messages/{message}', [MessageController::class, 'patch'])
        ->name('chatly.messages.update');

    Route::controller(ConversationController::class)->group(function () {
        Route::get('', 'index')->name('chatly.index');
        Route::post('', 'store')->name('chatly.store');
        Route::get('/{id}', 'show')->name('chatly.show');
        Route::delete('/{id}', 'destroy')->name('chatly.destroy');
    });
});
