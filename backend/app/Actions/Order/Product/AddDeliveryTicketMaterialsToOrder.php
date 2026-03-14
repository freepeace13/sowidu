<?php

namespace App\Actions\Order\Product;

use App\Actions\Traits\AsAction;
use App\Events\Order\AddedOrderProduct;
use App\Models\Company;
use App\Models\DeliveryTicketMaterial;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AddDeliveryTicketMaterialsToOrder
{
    use AsAction;

    public function handle(User $user, Company $company, Order $order, array $inputs)
    {
        Gate::forUser($user)->authorize('manageProducts', $order);

        $validated = $this->validate($company, $inputs);

        $usedProducts = OrderService::make($user, $company)
            ->addDeliveryTicketMaterial($order, data_get($validated, 'products', []));

        $usedProducts->each(
            fn ($usedProduct) => event(
                new AddedOrderProduct($user, $order, $usedProduct),
            ),
        );
    }

    protected function validate(Company $company, array $inputs): array
    {
        return Validator::make($inputs, [
            'products' => [
                'required',
                'array',
            ],
            'is_delivery_ticket_materials' => 'required|boolean',
            'products.*' => [
                'required',
                'integer',
                'exists:delivery_ticket_materials,id',
                function ($attribute, $value, $fail) use ($company) {
                    $deliveryTicketMaterial = DeliveryTicketMaterial::with(['deliveryTicket'])->find($value);

                    if ($deliveryTicketMaterial->deliveryTicket->company_id !== $company->getKey()) {
                        $fail(trans('validation.company_not_owner'));
                    }
                },
            ],
        ])->validate();
    }
}
