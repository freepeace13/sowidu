<?php

use Illuminate\Support\Facades\Route;
use Modules\DeliveryTicket\Http\Controllers\Inertia\DeliveryTicketController;
use Modules\DeliveryTicket\Http\Controllers\Inertia\DeliveryTicketDocumentController;
use Modules\DeliveryTicket\Http\Controllers\Inertia\DeliveryTicketMaterialController;

Route::controller(DeliveryTicketController::class)
    ->group(function () {
        Route::get('', 'index')
            ->name('delivery_tickets.index');
        Route::get('{deliveryTicket}', 'show')
            ->name('delivery_tickets.show');
        Route::post('store', 'store')
            ->name('delivery_tickets.store');
        Route::patch('{deliveryTicket}/update', 'update')
            ->name('delivery_tickets.update');
        Route::put('{deliveryTicket}/delivery-address-update', 'deliveryAddressUpdate')
            ->name('delivery_tickets.delivery-address.update');
        Route::put('{deliveryTicket}/deliverer-update', 'delivererUpdate')
            ->name('delivery_tickets.deliverer.update');
        Route::delete('{deliveryTicket}/destroy', 'destroy')
            ->name('delivery_tickets.destroy');
    });

Route::controller(DeliveryTicketMaterialController::class)
    ->prefix('{deliveryTicket}/materials')
    ->group(function () {
        Route::post('store', 'store')
            ->name('delivery_tickets.materials.store');

        Route::patch('{material}/update', 'update')
            ->name('delivery_tickets.materials.update');

        Route::delete('{material}/destroy', 'destroy')
            ->name('delivery_tickets.materials.destroy');
    });

Route::controller(DeliveryTicketDocumentController::class)
    ->prefix('{deliveryTicket}/documents')
    ->group(function () {
        Route::post('', 'store')->name('delivery_tickets.documents.store');
        Route::delete('{document}/destroy', 'destroy')
            ->name('delivery_tickets.documents.destroy');
    });
