<?php

use App\Http\Api\Controllers\V1\Media;
use Illuminate\Support\Facades\Route;

Route::get('/', Media\FileListController::class);
Route::post('/upload', Media\StoreFileController::class);

Route::get('/trash', [Media\FileTrashController::class, 'index']);
Route::delete('/{media}/trash', [Media\FileTrashController::class, 'destroy']);
Route::post('/{media}/trash', [Media\FileTrashController::class, 'restore']);

Route::get('/{media}/users', Media\FileUsersController::class);
Route::put('/{media}/share', Media\ShareFileController::class);
Route::delete('/{media}/share', Media\UnshareFileController::class);
Route::get('/{media}/shareable-users', Media\ShareableUsersController::class);

Route::get('/{media}', Media\FileDetailsController::class);
Route::patch('/{media}', Media\UpdateFileDetailsController::class);
