<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\StopOrderTimeTracking;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Order;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;
use Modules\WorkLogs\Models\WorkLog;

class StopOrderTimeTrackController extends InertiaController
{
    use InteractsWithImpersonator;

    public function __invoke(Request $request, Order $order, WorkLog $workLog, StopOrderTimeTracking $action)
    {
        $action->stop($request->user(), $this->getCurrentCompany(), $order, $workLog);

        return back();
    }
}
