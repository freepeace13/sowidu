<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\AcceptResponseOnOrder;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Order;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;

class ResponseAcceptController extends InertiaController
{
    use InteractsWithImpersonator;

    public function __invoke(
        Request $request,
        Order $order,
        AcceptResponseOnOrder $acceptResponseOnOrder,
    ) {
        $acceptResponseOnOrder->accept(
            $request->user(),
            $order,
            $request->all(),
            $this->getCurrentTeam(),
        );

        return back(303);
    }
}
