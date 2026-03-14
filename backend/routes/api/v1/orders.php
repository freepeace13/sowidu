<?php

use App\Http\Api\Controllers\V1\Orders\GetIncomingOrdersController;
use App\Http\Api\Controllers\V1\Orders\GetOutgoingOrdersController;
use App\Http\Api\Controllers\V1\Orders\ShowOrderController;
use App\Http\Api\Controllers\V1\Orders\UpdateOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/incoming', GetIncomingOrdersController::class);
Route::get('/outgoing', GetOutgoingOrdersController::class);
Route::get('/{order}', ShowOrderController::class);
Route::patch('/{order}', UpdateOrderController::class);
