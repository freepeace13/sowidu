<?php

use App\Enums\Permissions;
use App\Http\Controllers\Inertia\Catalog\CatalogItemController;
use App\Http\Middleware\Web\CatalogHandleInertiaRequests;
use Illuminate\Support\Facades\Route;

Route::prefix('catalogues')
    ->middleware([
        'auth',
        CatalogHandleInertiaRequests::class,
        'permission:' . Permissions::CAN_ACCESS_CATALOG,
    ])
    ->group(function () {
        // catalogs.items.*
        Route::prefix('items')
            ->group(function () {
                Route::get('', [CatalogItemController::class, 'index'])
                    ->name('catalogs.items.index');

                Route::post('store', [CatalogItemController::class, 'store'])
                    ->name('catalogs.items.store');

                Route::patch('update/{item}', [CatalogItemController::class, 'update'])
                    ->name('catalogs.items.update');

                Route::delete('destroy/{item}', [CatalogItemController::class, 'destroy'])
                    ->name('catalogs.items.destroy');
            });
    });
