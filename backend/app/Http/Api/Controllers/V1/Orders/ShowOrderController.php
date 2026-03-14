<?php

namespace App\Http\Api\Controllers\V1\Orders;

use App\Http\Api\Resources\V1\Orders\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderTimeLogService;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class ShowOrderController extends RestfulController
{
    public function __invoke(Request $request, Order $order)
    {
        // $timeLogsService = new OrderTimeLogService($order);
        // $totalTimeRendered = $timeLogsService->getOrderTotalTimeRendered();

        return (new OrderResource($order))
            ->withDeliveryAddress();
    }
}
