<?php

namespace App\Http\Api\Resources\V1\Orders;

use App\Http\Api\Resources\V1\PlaceResource;
use App\Models\Company;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Order\Traits\WithOrderStatusStyles;
use Packages\RestApi\Resources\JsonResource;

class OrderResource extends JsonResource
{
    use WithOrderStatusStyles;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'orderNumber' => $this->order_number,
            'description' => $this->description,
            'orderDate' => $this->order_date,
            'startDate' => $this->planned_start_date,
            'endDate' => $this->planned_finish_date,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }

    public function withStatus(User $user, ?Company $company = null)
    {
        $service = OrderService::make($user, $company);
        $toStatus = $service->getNeededResponseValue($this->resource)?->name ?? $this->status->name;

        return $this->state(function (array $attributes) use ($toStatus) {
            return [
                'status' => [
                    'title' => $this->orderStatusTitle($toStatus),
                    'description' => $this->orderStatusDescription($toStatus),
                    'value' => $this->status,
                    'text' => snake_to_readable($toStatus),
                ],
            ];
        });
    }

    public function withDeliveryAddress()
    {
        return $this->state(function (array $attributes) {
            return [
                'deliveryAddress' => (new PlaceResource($this->deliveryAddress))
                    ->withId()
                    ->withGoogleMapUrl()
                    ->withShortFullAddress()
                    ->resolve(),
            ];
        });
    }

    public function withType(User $user, ?Company $team)
    {
        $service = OrderService::make($user, $team);
        $orderType = $service->orderTypeOnAuth($this->resource);

        return $this->state(fn () => [
            'type' => [
                'value' => $orderType->value,
                'description' => $orderType->description(),
            ],
        ]);
    }
}
