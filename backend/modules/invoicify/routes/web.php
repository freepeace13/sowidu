<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoicify\Http\Controllers\ExportPdfController;
use Modules\Invoicify\Http\Controllers\MarkAsPaidController;
use Modules\Invoicify\Http\Controllers\SendToClientController;
use Modules\Invoicify\Http\Controllers\StoreManualItemController;
use Modules\Invoicify\Http\Controllers\StreamPdfController;
use Modules\Invoicify\Http\Controllers\UpdateManualItemController;

Route::patch('{invoice}/manual-items', UpdateManualItemController::class)
    ->name('invoicify.manual_items.update');

Route::post('{invoice}/manual-items', StoreManualItemController::class)
    ->name('invoicify.manual_items.store');

Route::post('{invoice}/mark-as-paid', MarkAsPaidController::class)
    ->name('invoicify.mark_as_paid');
Route::post('{invoice}/send-to-client', SendToClientController::class)
    ->name('invoicify.send_to_client');

Route::get('{invoice}/pdf', StreamPdfController::class)
    ->name('invoicify.pdf.stream');
Route::get('{invoice}/download', ExportPdfController::class)
    ->name('invoicify.pdf.download');

require __DIR__ . '/invoicify.php';
