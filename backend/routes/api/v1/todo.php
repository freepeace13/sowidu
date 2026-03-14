<?php

use App\Http\Api\Controllers\V1\Todo\BoardController;
use App\Http\Api\Controllers\V1\Todo\BoardGroupController;
use App\Http\Api\Controllers\V1\Todo\SubscriptionController;
use App\Http\Api\Controllers\V1\Todo\TaskCommentController;
use App\Http\Api\Controllers\V1\Todo\TaskController;
use App\Http\Api\Controllers\V1\Todo\TaskMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/boards', [BoardController::class, 'index']);
Route::post('/boards', [BoardController::class, 'store']);
Route::get('/boards/{board}', [BoardController::class, 'show']);
Route::post('/boards/{board}/groups', [BoardGroupController::class, 'store']);
Route::delete('/boards/{board}/groups', [BoardGroupController::class, 'destroy']);
Route::get('/boards/{board}/subscribers', [SubscriptionController::class, 'index']);
Route::post('/boards/{board}/subscribers', [SubscriptionController::class, 'store']);
Route::put('/boards/{board}/subscribers/{subscriber}', [SubscriptionController::class, 'update']);
Route::delete('/boards/{board}/subscribers', [SubscriptionController::class, 'destroy']);
Route::get('/boards/{board}/tasks', [TaskController::class, 'index']);
Route::post('/boards/{board}/tasks', [TaskController::class, 'store']);
Route::get('/boards/{board}/tasks/{task}', [TaskController::class, 'show']);
Route::put('/boards/{board}/tasks/{task}', [TaskController::class, 'update']);
Route::get('/boards/{board}/tasks/{task}/comments', [TaskCommentController::class, 'index']);
Route::post('/boards/{board}/tasks/{task}/comments', [TaskCommentController::class, 'store']);
Route::post('/boards/{board}/tasks/{task}/members', [TaskMemberController::class, 'store']);
Route::delete('/boards/{board}/tasks/{task}/members/{subscriber}', [TaskMemberController::class, 'destroy']);
