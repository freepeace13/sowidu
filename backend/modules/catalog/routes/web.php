<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\Inertia\CatalogItemController;

Route::get('/', [CatalogItemController::class, 'index'])->name('catalog.index');
Route::post('/', [CatalogItemController::class, 'store'])->name('catalog.store');
Route::patch('/{item}', [CatalogItemController::class, 'update'])->name('catalog.update');
Route::delete('/{item}', [CatalogItemController::class, 'destroy'])->name('catalog.destroy');
