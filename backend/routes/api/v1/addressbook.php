<?php

use App\Http\Api\Controllers\V1\Addressbook\AddressbookController;
use App\Http\Api\Controllers\V1\Addressbook\AddressbookTrashController;
use Illuminate\Support\Facades\Route;

/**
 * @route /api/v1/addressbooks/trash
 */
Route::controller(AddressbookTrashController::class)
    ->prefix('trash')
    ->group(function () {
        Route::get('', 'index');
        Route::patch('{addressbook}/restore', 'restore');
    });

/**
 * @route /api/v1/addressbooks
 */
Route::controller(AddressbookController::class)
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('{addressbook}', 'show');
        Route::patch('{addressbook}/update', 'update');
        Route::delete('{addressbook}/trash', 'destroy');
    });
