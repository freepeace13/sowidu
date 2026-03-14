<?php

use App\Enums\Permissions;
use App\Modules\Invoice\Controllers\BulkInvoicePdfExportController;
use App\Modules\Invoice\Controllers\InvoiceController;
use App\Modules\Invoice\Controllers\InvoiceDeductionController;
use App\Modules\Invoice\Controllers\InvoiceDocumentController;
use App\Modules\Invoice\Controllers\InvoiceDurationController;
use App\Modules\Invoice\Controllers\InvoiceItemController;
use App\Modules\Invoice\Controllers\InvoiceManualItemController;
use App\Modules\Invoice\Controllers\InvoiceMarkPaidController;
use App\Modules\Invoice\Controllers\InvoicePaymentController;
use App\Modules\Invoice\Controllers\InvoicePdfController;
use App\Modules\Invoice\Controllers\InvoiceTaxController;
use App\Modules\Invoice\Controllers\PreviewInvoiceController;
use App\Modules\Invoice\Controllers\SendInvoiceToClientController;
use App\Modules\Invoice\Controllers\UpdateInvoiceItemPriceController;
use App\Modules\Invoice\Controllers\UpdateInvoicePreviewController;
use App\Modules\Invoice\Middlewares\InvoiceInertiaHandler;
use App\Modules\Invoice\Middlewares\InvoicePreviewInertiaHandler;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('invoices')
    ->group(function () {
        Route::middleware([
            'auth',
            'impersonating',
            InvoiceInertiaHandler::class,
            'permission:' . Permissions::CAN_ACCESS_INVOICES,
        ])
            ->group(function () {
                Route::controller(InvoiceController::class)
                    ->prefix('')
                    ->group(function () {
                        Route::get('', 'index')->name('invoices.index');
                        Route::get('{invoice}', 'show')->name('invoices.show');
                        Route::post('store', 'store')->name('invoices.store');
                        Route::patch('{invoice}/update', 'update')->name('invoices.update');
                        Route::delete('{invoice}/destroy', 'destroy')->name('invoices.destroy');

                        Route::patch(
                            '{invoice}/update/subject-and-description',
                            UpdateInvoicePreviewController::class,
                        )->name('invoices.update.subject_and_description');

                        Route::post('bulk-export', BulkInvoicePdfExportController::class)
                            ->name('invoices.bulk_export');
                    });

                Route::post('{invoice}/mark-as-paid', InvoiceMarkPaidController::class)
                    ->name('invoices.mark_as_paid');

                Route::post('{invoice}/send-to-client', SendInvoiceToClientController::class)
                    ->name('invoices.send_to_client');

                Route::controller(InvoiceDocumentController::class)
                    ->prefix('{invoice}/documents')
                    ->group(function () {
                        Route::post('', 'store')->name('invoices.documents.store');
                        Route::delete('{document}/destroy', 'destroy')
                            ->name('invoices.documents.destroy');
                    });

                Route::controller(InvoiceItemController::class)
                    ->prefix('{invoice}/items')
                    ->group(function () {
                        Route::post('', 'store')->name('invoices.items.store');
                        Route::patch('{item}/update', 'update')->name('invoices.items.update');
                        Route::delete('{item}/destroy', 'destroy')
                            ->name('invoices.items.destroy');
                        Route::patch(
                            '{item}/update-price',
                            UpdateInvoiceItemPriceController::class,
                        )->name('invoices.items.update_price');
                    });

                Route::controller(InvoiceTaxController::class)
                    ->prefix('{invoice}/taxes')
                    ->group(function () {
                        Route::post('', 'store')->name('invoices.taxes.store');
                        Route::delete('{tax}/destroy', 'destroy')
                            ->name('invoices.taxes.destroy');
                    });

                Route::controller(InvoiceDeductionController::class)
                    ->prefix('{invoice}/deductions')
                    ->group(function () {
                        Route::post('store/invoice', 'deduct')->name('invoices.deduct');
                        Route::post('store/manual', 'manual')->name('invoices.deduct.manual');

                        Route::delete(
                            '{invoiceDeduction}/remove',
                            'remove',
                        )->name('invoices.deductions.remove');
                    });

                // Route::delete('{invoice}/deductions-manual/{deductionManual}', [InvoiceDeductionController::class, 'removeManual'])->name('invoice.deduction.remove.manual');

                Route::patch('{invoice}/duration', InvoiceDurationController::class)->name('invoice.duration.update');

                // invoice/{invoice}/payments/*
                Route::controller(InvoicePaymentController::class)
                    ->prefix('{invoice}/payments')
                    ->group(function () {
                        Route::post('store', 'store')->name('invoices.payments.store');
                        Route::patch('{invoicePayment}/update', 'update')->name('invoices.payments.update');
                        Route::delete('{invoicePayment}/destroy', 'destroy')->name('invoices.payments.destroy');
                    });

                // invoice/{invoice}/manual-items/*
                Route::controller(InvoiceManualItemController::class)
                    ->prefix('{invoice}/manual-items')
                    ->group(function () {
                        Route::post('store', 'store')->name('invoices.manual_items.store');
                        Route::patch('{invoiceManualItem}/update', 'update')
                            ->name('invoices.manual_items.update');
                    });
            });

        Route::middleware([
            'guestOrAuth',
            InvoicePreviewInertiaHandler::class,
        ])->group(function () {

            Route::get('{invoice}/preview', PreviewInvoiceController::class)
                ->name('invoices.preview');

            Route::controller(InvoicePdfController::class)
                ->prefix('{invoice}/pdf')
                ->group(function () {
                    Route::get('stream', 'stream')
                        ->name('invoice.pdf.stream');

                    Route::get('download', 'download')
                        ->name('invoice.pdf.download');
                });
        });
    });
