<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\StartOrderTimeTracking;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Order;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;

class StartOrderTimeTrackController extends InertiaController
{
    use InteractsWithImpersonator;

    public function __invoke(Request $request, Order $order, StartOrderTimeTracking $action)
    {
        $action->start($request->user(), $this->getCurrentTeam(), $order);

        return back();
    }
}
