<?php

use App\Enums\Permissions;
use App\Http\Controllers\Json\Invoice\GetInvoicesController;
use App\Http\Controllers\Json\Invoice\GetInvoicesSummariesController;
use App\Http\Controllers\Json\Invoice\ShowInvoiceController;

/**
 * Invoices
 */
Route::middleware(['permission:' . Permissions::CAN_ACCESS_INVOICES])
    ->prefix('invoices')
    ->group(function () {
        Route::get('', GetInvoicesController::class)
            ->name('json.invoices.index');

        Route::get('summaries', GetInvoicesSummariesController::class)
            ->name('json.invoices.summaries');

        Route::get(
            '{invoice}/show',
            ShowInvoiceController::class,
        )->name('json.invoices.show');
    });
