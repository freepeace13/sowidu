<?php

namespace App\Actions\Order\Product;

use App\Actions\Traits\AsAction;
use App\Events\Order\OrderProductUpdated;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateProductQuantityOnOrder
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Order $order,
        OrderProduct $orderProduct,
        array $inputs,
    ) {
        Gate::forUser($user)->authorize('manageProducts', $order);

        $validated = $this->validate($inputs);

        $orderProduct->update($validated);

        event(new OrderProductUpdated($user, $order, $orderProduct));
    }

    protected function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'quantity' => [
                'required',
                'integer',
                'gte:1',
            ],
        ])->validate();
    }
}
