<?php

use App\Http\Controllers\Json\Invoice\InvoicePreviewLayoutController;

/**
 * Public routes
 */
Route::prefix('public')
    ->group(function () {
        // TODO - routes are not used anymore
        Route::controller(InvoicePreviewLayoutController::class)
            ->prefix('invoices/{invoice}/preview-layout')
            ->group(function () {
                Route::post('', 'store')->name('json.invoices.preview-layout.store');
                Route::delete('', 'destroy')->name('json.invoices.preview-layout.destroy');
            });
    });
