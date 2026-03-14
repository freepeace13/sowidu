<?php

use App\Http\Controllers\Json\Catalog\GetCatalogItemController;

Route::middleware(['auth', 'json'])
    ->name('json.')
    ->group(function () {
        Route::get('catalog/items', GetCatalogItemController::class)->name('catalog.items.index');
    });
