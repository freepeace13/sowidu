<?php

use App\Enums\Permissions;
use App\Http\Controllers\Inertia\DeliveryTicket\DeliveryTicketController;
use App\Http\Controllers\Inertia\DeliveryTicket\DeliveryTicketDocumentController;
use App\Http\Controllers\Inertia\DeliveryTicket\DeliveryTicketMaterialController;
use App\Http\Middleware\Web\DeliveryTicketsInertiaHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('delivery-tickets')
    ->middleware([
        'auth',
        'impersonating',
        DeliveryTicketsInertiaHandler::class,
        'permission:' . Permissions::CAN_ACCESS_DELIVERY_TICKETS,
    ])
    ->group(function () {
        Route::controller(DeliveryTicketController::class)
            ->prefix('')
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
    });
