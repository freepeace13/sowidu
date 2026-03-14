<?php

use App\Enums\Permissions;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Inertia\Media\AjaxController;
use App\Http\Controllers\Inertia\Media\FileUploadController;
use App\Http\Controllers\Inertia\Media\FolderController;
use App\Http\Controllers\Inertia\Media\MediaController;
use App\Http\Controllers\Inertia\Media\MediaTagAddressController;
use App\Http\Controllers\Inertia\Media\MediaTagCategoryController;
use App\Http\Controllers\Inertia\Media\RemoveMediaTagCategoryController;
use App\Http\Controllers\Inertia\Media\ShareController;
use App\Http\Controllers\Inertia\Media\StarredController;
use App\Http\Controllers\Inertia\Media\TrashController;
use App\Http\Middleware\MediaMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/media-library/{media}/{path}', [FileController::class, 'serve'])
//     ->middleware(['auth:web,sanctum'])
//     ->where('path', '.*')
//     ->name('media-library');

// Route::get('/media/files/{media}/download', [FileController::class, 'download'])
//     ->middleware(['auth:web,sanctum'])
//     ->name('media.files.download');

Route::prefix('apps/media')
    ->middleware([
        'auth',
        'permission:' . Permissions::CAN_ACCESS_MEDIA,
        MediaMiddleware::class,
    ])->group(function () {
        Route::controller(TrashController::class)
            ->prefix('trash')
            ->group(function () {
                Route::get('', 'index')->name('media.trash');
                Route::put('{media}/restore', 'restore')
                    ->name('media.trash.restore');
                Route::delete('empty', 'emptyTrash')->name('media.trash.empty');
                Route::delete('{media}', 'destroy')->name('media.trash.destroy');
            });

        Route::controller(MediaController::class)
            ->group(function () {
                Route::get('', 'index')
                    ->name('media.drive.index');

                Route::put('/{media}/rename', 'rename')
                    ->name('media.rename');

                Route::delete('/{media}', 'destroy')
                    ->name('media.destroy');

                Route::put('/{media}/move', 'move')
                    ->name('media.move');

                Route::get('/{media}', 'show')
                    ->name('media.show');
            });

        Route::controller(StarredController::class)
            ->prefix('starred')
            ->group(function () {
                Route::get('', 'index')->name('media.starred');
                Route::post('', 'store')->name('media.starred.store');
                Route::delete('/{media}', 'destroy')->name('media.starred.destroy');
            });

        Route::controller(ShareController::class)
            ->prefix('/{media}/share')
            ->group(function () {
                Route::put('', 'update')
                    ->name('media.share.update');

                Route::post('', 'store')
                    ->name('media.share.store');

                Route::delete('', 'destroy')
                    ->name('media.share.destroy');
            });

        Route::controller(FolderController::class)
            ->prefix('/folders/{folder?}')
            ->group(function () {
                Route::get('', 'show')->name('media.folders.show');
                Route::post('', 'store')->name('media.folders.store');
            });

        Route::get('/d/folders/{folder}', [MediaController::class, 'index'])
            ->name('media.drive.folders.show');

        // apps/media/{media}/tag-address
        Route::controller(MediaTagAddressController::class)
            ->prefix('/{media}/tag-address')
            ->group(function () {
                Route::post('', 'store')
                    ->name('media.tag_address.store');
                Route::delete('', 'destroy')
                    ->name('media.tag_address.destroy');
            });

        Route::post('/{media}/tag-category', MediaTagCategoryController::class)
            ->name('media.tag_category.store');

        Route::delete('/{media}/tag-category/destroy', RemoveMediaTagCategoryController::class)
            ->name('media.tag_category.destroy');
    });

Route::get('/json/media/{media}/share/settings', [AjaxController::class, 'getShareSettings'])
    ->name('json.media.share.settings');

Route::get('/json/media/{media}/suggestions', [AjaxController::class, 'getSuggestions'])
    ->name('json.media.share.suggestions');

Route::get('/json/media/images', [AjaxController::class, 'getImages'])
    ->name('json.media.images');

Route::put('/json/media/{media}/custom-fields', [AjaxController::class, 'updateCustomField'])
    ->name('json.media.customField.update');

Route::put('/json/media/{media}/category', [AjaxController::class, 'updateCategory'])
    ->name('json.media.category.update')
    ->middleware('auth');

Route::post('/media/files/upload', FileUploadController::class)
    ->name('media.files.upload')
    ->middleware('auth');

Route::post('/media/files/{file}/update', FileUploadController::class)
    ->name('media.files.update')
    ->middleware('auth');
