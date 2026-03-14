<?php

namespace App\Actions\Order;

use App\Enums\OrderEvent;
use App\Events\Order\EmployeeStoppedWorkingOnOrder;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Order\OrderTimeLogService;
use Modules\WorkLogs\Models\WorkLog;

class StopOrderTimeTracking
{
    public function stop(User $user, Company $company, Order $order, WorkLog $workLog)
    {
        $service = OrderService::make($user, $company);

        // Validate user is allowed to stop time tracking for the order
        abort_unless(
            $service->canStopTimeLog($order, $workLog),
            403,
            'You are not allowed to stop time tracking for this order!',
        );

        // Stop WorkLog
        OrderTimeLogService::make($order)->stop($workLog, $user, $company);

        // Log time tracking stop event
        activity_log($order)
            ->orderLog()
            ->setCauser($user)
            ->updateStatus(OrderEvent::FINISH_WORKING);

        // Dispatch Event
        event(new EmployeeStoppedWorkingOnOrder($order, $user));
    }
}
