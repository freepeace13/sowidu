<?php

use App\Enums\Permissions;
use App\Http\Middleware\Web\InvoiceInertiaHandler;
use Illuminate\Support\Facades\Route;
use Modules\Invoicify\Http\Controllers\BulkExportPdfController;
use Modules\Invoicify\Http\Controllers\UpdateInvoiceController;

Route::group([
    'middleware' => [
        'auth',
        'impersonating',
        InvoiceInertiaHandler::class,
        'permission:' . Permissions::CAN_ACCESS_INVOICES,
    ],
], function () {
    Route::patch('/{invoice}/update', UpdateInvoiceController::class)->name('invoicify.update');
    Route::post('/bulk-export', BulkExportPdfController::class)->name('invoicify.bulk-export');
});
