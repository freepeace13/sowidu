<?php

namespace App\Http\Controllers\Json\Order;

use App\Http\Controllers\Json\BaseController;
use App\Http\Controllers\Traits\WithOrderService;
use App\Models\Order;
use App\Transformers\Order\OrderTransformer;

class OrderJsonController extends BaseController
{
    use WithOrderService;

    public function show(Order $order)
    {
        $service = $this->createOrderService();

        if ($service->clientIs($order, $this->account())) {
            flash_error('You cannot update an order if you already confirmed it.');

            return false;
        }

        abort_unless(
            $service->isCurrentlyOwned($order) || $service->isOrderedByCurrentUser($order),
            404,
            'Order not found!',
        );

        return (new OrderTransformer($order))
            ->withClientFullDetails($order->client)
            ->withDeliveryAddress()
            ->withContractorDetails()
            ->resolve();
    }
}
