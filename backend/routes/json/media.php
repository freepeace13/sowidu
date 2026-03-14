<?php

use App\Http\Controllers\Json\Media\MediaAddressTagController;
use App\Http\Controllers\Json\Media\MediaFileListController;
use Illuminate\Support\Facades\Route;

/**
 * Will fetch media address_tags if any
 *
 * @url - /json/media/{media}/address-tags
 *
 * @param string media
 */
Route::get('media/{media}/address-tags', MediaAddressTagController::class)
    ->whereUuid('media')
    ->name('json.media.address_tags.show');

Route::get('files', MediaFileListController::class)
    ->name('media.drive.files.index');
