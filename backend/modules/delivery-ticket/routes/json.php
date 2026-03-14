<?php

use Illuminate\Support\Facades\Route;
use Modules\DeliveryTicket\Http\Controllers\Json\GetDeliveryTicketController;
use Modules\DeliveryTicket\Http\Controllers\Json\ShowDeliveryTicketController;
use Modules\DeliveryTicket\Http\Controllers\Json\ShowDeliveryTicketMaterialsController;

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
