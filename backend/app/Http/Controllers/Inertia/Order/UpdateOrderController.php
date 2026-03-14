<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\UpdateOrder;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Order;
use Illuminate\Http\Request;

class UpdateOrderController extends InertiaController
{
    public function __invoke(Request $request, Order $order)
    {
        $order = UpdateOrder::run($request->user(), $request->company(), $order, $request->all());

        if (filled($order)) {
            flash_success(trans('order.messages.updated'));
        }

        return back();
    }
}
