<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/media-library/{media}/{path}', [FileController::class, 'serve'])
    ->where('path', '.*')
    ->withoutMiddleware('throttle:api')
    ->name('media-library');

Route::get('/media/files/{media}/download', [FileController::class, 'download'])
    ->name('media.files.download');
