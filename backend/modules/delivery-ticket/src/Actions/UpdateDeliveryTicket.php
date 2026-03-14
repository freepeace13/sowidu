<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use App\Models\Addressbook;
use App\Models\Order;
use App\Rules\OwnedByCompany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDeliveryTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class UpdateDeliveryTicket implements UpdateDeliveryTicketContract
{
    public function handle(
        $user,
        $company,
        DeliveryTicket $deliveryTicket,
        array $inputs,
    ) {
        Gate::forUser($user)->authorize('update', $deliveryTicket);

        $validated = $this->validate($inputs);

        $deliveryTicket->update(Arr::only($validated, ['external_id', 'delivery_date']));

        $deliveryTicket->deliveryAddress()
            ->associate(data_get($validated, 'delivery_address.id'));
        $deliveryTicket->order()
            ->associate(data_get($validated, 'order.id'));
        $deliveryTicket->deliverer()
            ->associate(data_get($validated, 'deliverer.id'));

        $deliveryTicket->save();
    }

    protected function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'deliverer' => 'required|array',
            'deliverer.id' => [
                'required',
                'integer',
                new OwnedByCompany(Addressbook::class, 'team_id'),
            ],
            'order' => 'required|array',
            'order.id' => [
                'required',
                'integer',
                new OwnedByCompany(Order::class, 'team_id'),
            ],
            'external_id' => 'required|alpha_dash',
            'delivery_address' => 'required|array',
            'delivery_address.id' => [
                'required',
                'integer',
                'exists:places,id',
            ],
            'delivery_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
        ])->validate();
    }
}
