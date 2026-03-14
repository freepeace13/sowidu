<?php

namespace App\Actions\Order;

use App\Enums\OrderEvent;
use App\Enums\OrderStatus;
use App\Events\Order\EmployeeStartedWorkingOnOrder;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Order\OrderTimeLogService;
use Modules\WorkLogs\Contracts\WorkLogServiceInterface;

class StartOrderTimeTracking
{
    public function __construct(
        protected WorkLogServiceInterface $workLogService,
    ) {}

    public function start(User $user, Company $company, Order $order)
    {
        $service = OrderService::make($user, $company);

        // Validate user is allowed to `start` time tracking for the order
        abort_unless(
            $service->canStartTimeLog($order),
            403,
            'You are not allowed to start time tracking for this order!',
        );

        // Validate employee is free to work on the order
        if ($this->employeeIsCurrentlyWorkingOnOtherOrder($user, $company)) {
            return throw_validation(trans('order.errors.employee-cannot-work-on-order', [
                'order-link' => route(
                    'orders.show',
                    [
                        'order' => $this->getOrderThatUserCurrentlyWorkedOn($user, $company)
                            ?->order_id,
                    ],
                ),
            ]), 'employee_still_working_on_other_order');
        }

        if ($order->status === OrderStatus::COMMISSIONED) {
            // Move Order to `Started` first
            $order = $service->acceptOrder($order, OrderStatus::STARTED);
        }

        if (!OrderTimeLogService::make($order)->employeeCanStartWorking($user, $company)) {
            // Accept the order then start the time tracking
            app(AcceptResponseOnOrder::class)->accept(
                $user,
                $order,
                [
                    'value' => $service->getNeededResponseValue($order)
                        ?->value,
                ],
                $company,
            );
        }

        // Start WorkLog
        OrderTimeLogService::make($order)->start($user, $company);

        // Log time tracking start event
        activity_log($order)
            ->orderLog()
            ->setCauser($user)
            ->withProperties(['currently_working' => true])
            ->updateStatus(OrderEvent::START_WORKING);

        // Dispatch Event
        event(new EmployeeStartedWorkingOnOrder($order, $user));
    }

    protected function employeeIsCurrentlyWorkingOnOtherOrder(User $user, Company $company): bool
    {
        return $this->workLogService->make($user, $company)
            ->byEmployee($user)
            ->currentlyWorking()
            ->exists();
    }

    protected function getOrderThatUserCurrentlyWorkedOn(User $user, Company $company)
    {
        return $this->workLogService->make($user, $company)
            ->byEmployee($user)
            ->currentlyWorking()
            ->first();
    }
}
