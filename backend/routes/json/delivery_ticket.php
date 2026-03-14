<?php

use App\Enums\Permissions;
use App\Http\Controllers\Json\DeliveryTicket\GetDeliveryTicketController;
use App\Http\Controllers\Json\DeliveryTicket\ShowDeliveryTicketController;
use App\Http\Controllers\Json\DeliveryTicket\ShowDeliveryTicketMaterialsController;

/**
 * Delivery Tickets
 */
Route::middleware(['permission:' . Permissions::CAN_ACCESS_DELIVERY_TICKETS])
    ->prefix('delivery-tickets')
    ->group(function () {
        Route::get('', GetDeliveryTicketController::class)
            ->name('json.delivery_tickets.index');

        Route::get(
            '{deliveryTicket}/show',
            ShowDeliveryTicketController::class,
        )->name('json.delivery_tickets.show');

        Route::get(
            '{deliveryTicket}/materials',
            ShowDeliveryTicketMaterialsController::class,
        )->name('json.delivery_tickets.materials.show');
    });
