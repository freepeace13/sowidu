<?php

use App\Http\Controllers\Json\Invoice\InvoiceDeductionJsonController;
use App\Http\Controllers\Json\Order\GetOrderDeliveryTicketMaterialsController;
use App\Http\Controllers\Json\Order\GetOrderInvoicesController;
use App\Http\Controllers\Json\Order\OrderJsonController;
use App\Http\Middleware\EnsureCanParticipateOnOrder;
use Illuminate\Support\Facades\Route;

Route::get('/order/{order}', [OrderJsonController::class, 'show'])
    ->name('json.order.show');

Route::get('/order/{order}/invoices/{invoice}', [InvoiceDeductionJsonController::class, 'forDeduction'])
    ->name('json.order.invoices.deductable');

Route::middleware(EnsureCanParticipateOnOrder::class)
    ->group(function () {
        Route::get('/order/{order}/delivery-ticket-materials', GetOrderDeliveryTicketMaterialsController::class)
            ->name('json.order.delivery-ticket-materials');

        Route::get(
            '/order/{order}/invoices',
            GetOrderInvoicesController::class,
        )
            ->name('json.order.invoices');
    });
