<?php

namespace App\Actions\Order;

use App\Enums\OrderStatus;
use App\Events\Order\OrderAcceptedResponse;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Modules\WorkLogs\Contracts\WorkLogServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class AcceptResponseOnOrder
{
    public function __construct(
        protected WorkLogServiceInterface $workLogService,
    ) {}

    public function accept(
        User $user,
        Order $order,
        array $inputs,
        ?Company $company = null,
    ): Order {
        Gate::forUser($user)->authorize('acceptOrConfirm', $order);
        $service = OrderService::make($user, $company);

        // Validate response confirmation `OrderStatus` value
        $validated = Validator::make($inputs, [
            'value' => [
                'required',
                'integer',
                new Enum(OrderStatus::class),
                'size:' . $service->getNeededResponseValue($order)
                    ?->value,
            ],
        ])->validate();

        $response = $service->acceptingGetResponseValue($order, $validated['value']);

        // Verify that user can accept this order
        abort_unless(
            $service->canResponseToOrder($order) || $service->canForceConfirm($order, $response),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'You are not allowed to make this action.',
        );

        if ($response === OrderStatus::READY_FOR_REVIEW && $this->employeesStillWorking($user, $order, $company)) {
            // Validate if response is `For Review` ensure that no employees are still working on the Order
            flash_error(trans('order.notifications.employee-still-working'));

            return $order;
        }

        $order = $service->acceptOrder($order, $response);

        // Trigger event
        event(new OrderAcceptedResponse($order, $user, $company));

        return $order;
    }

    protected function employeesStillWorking(User $user, Order $order, $company = null): bool
    {
        return $this->workLogService->make($user, $company)
            ->onCompanyOnly($company)
            ->onOrder($order)
            ->currentlyWorking()
            ->exists();
    }
}
