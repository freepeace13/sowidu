<?php

use Illuminate\Support\Facades\Route;
use Modules\Todos\Http\Controllers\BoardActivityController;
use Modules\Todos\Http\Controllers\BoardController;
use Modules\Todos\Http\Controllers\BoardGroupController;
use Modules\Todos\Http\Controllers\BoardLabelController;
use Modules\Todos\Http\Controllers\BoardPermissionController;
use Modules\Todos\Http\Controllers\DuplicateBoardController;
use Modules\Todos\Http\Controllers\ManualTaskController;
use Modules\Todos\Http\Controllers\SearchUserController;
use Modules\Todos\Http\Controllers\SubscriberController;
use Modules\Todos\Http\Controllers\TaskActivityController;
use Modules\Todos\Http\Controllers\TaskAttachmentController;
use Modules\Todos\Http\Controllers\TaskCommentController;
use Modules\Todos\Http\Controllers\TaskController;
use Modules\Todos\Http\Controllers\TaskLabelController;
use Modules\Todos\Http\Controllers\TaskMediaController;
use Modules\Todos\Http\Controllers\TaskMemberController;
use Modules\Todos\Http\Controllers\TaskTimeLogController;

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

Route::get('boards/task-group/{board}', [TaskController::class, 'showTaskGroup'])
    ->name('boards.task.tasks-group');

Route::post('boards/task/manual-task/{board}', [ManualTaskController::class, 'store'])
    ->name('boards.manual-task.store');
