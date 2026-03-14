<?php

use App\Enums\Permissions;
use App\Http\Controllers\Inertia\Todo\BoardActivityController;
use App\Http\Controllers\Inertia\Todo\BoardController;
use App\Http\Controllers\Inertia\Todo\BoardGroupController;
use App\Http\Controllers\Inertia\Todo\BoardLabelController;
use App\Http\Controllers\Inertia\Todo\BoardPermissionController;
use App\Http\Controllers\Inertia\Todo\DuplicateBoardController;
use App\Http\Controllers\Inertia\Todo\SearchUserController;
use App\Http\Controllers\Inertia\Todo\SubscriberController;
use App\Http\Controllers\Inertia\Todo\TaskActivityController;
use App\Http\Controllers\Inertia\Todo\TaskAttachmentController;
use App\Http\Controllers\Inertia\Todo\TaskCommentController;
use App\Http\Controllers\Inertia\Todo\TaskController;
use App\Http\Controllers\Inertia\Todo\TaskLabelController;
use App\Http\Controllers\Inertia\Todo\TaskMediaController;
use App\Http\Controllers\Inertia\Todo\TaskMemberController;
use App\Http\Controllers\Inertia\Todo\TaskTimeLogController;
use App\Http\Middleware\Web\TodoHandleInertiaRequests;
use Illuminate\Support\Facades\Route;

Route::name('todos.')
    ->prefix('apps/todos')
    ->middleware([
        'auth',
        'permission:' . Permissions::CAN_ACCESS_TODO,
        TodoHandleInertiaRequests::class,
    ])
    ->group(function () {
        Route::resource('boards', BoardController::class)
            ->except(['create', 'edit']);

        Route::resource('boards.groups', BoardGroupController::class)
            ->only(['store', 'update', 'destroy']);

        Route::resource('boards.labels', BoardLabelController::class)
            ->only(['store', 'update', 'destroy']);

        Route::post('boards/{board}/duplicate', DuplicateBoardController::class)
            ->name('boards.duplicate');

        Route::patch('boards/{board}/permissions', BoardPermissionController::class)->name('boards.permissions');

        Route::get('board/{board}/activities', BoardActivityController::class)->name('boards.activities');

        Route::resource('search/users', SearchUserController::class)
            ->only(['index', 'show']);

        Route::resource('boards.subscribers', SubscriberController::class)
            ->only(['store', 'destroy', 'index']);

        Route::resource('boards.tasks', TaskController::class)
            ->except(['create', 'edit']);

        Route::resource('boards.tasks.members', TaskMemberController::class)
            ->parameters(['members' => 'subscriber'])
            ->only(['store', 'destroy']);

        Route::resource('boards.tasks.labels', TaskLabelController::class)
            ->only(['store', 'destroy']);

        Route::resource('boards.tasks.comments', TaskCommentController::class)
            ->only(['store', 'update', 'destroy']);

        Route::resource('boards.tasks.attachments', TaskAttachmentController::class)
            ->only(['show', 'store', 'destroy']);

        Route::get('boards/{board}/tasks/{task}/activities', TaskActivityController::class)
            ->name('boards.tasks.activities');

        Route::apiResource('boards.tasks.time-logs', TaskTimeLogController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Route::get('media', TaskMediaController::class)->name('media');
    });
