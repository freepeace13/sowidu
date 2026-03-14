<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\DisapproveResponseOnOrder;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Order;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;

class ResponseDisapproveController extends InertiaController
{
    use InteractsWithImpersonator;

    public function __invoke(
        Request $request,
        Order $order,
        DisapproveResponseOnOrder $disapproveResponseOnOrder,
    ) {
        $disapproveResponseOnOrder->disapprove($request->user(), $order, $this->getCurrentTeam());

        return back(303);
    }
}
