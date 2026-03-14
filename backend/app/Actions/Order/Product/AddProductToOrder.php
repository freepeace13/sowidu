<?php

namespace App\Actions\Order\Product;

use App\Actions\Traits\AsAction;
use App\Events\Order\AddedOrderProduct;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Services\CatalogService;

class AddProductToOrder
{
    use AsAction;

    public function handle(User $user, Company $company, Order $order, array $inputs)
    {
        Gate::forUser($user)->authorize('manageProducts', $order);

        $validated = $this->validate($user, $company, $inputs);

        $usedProducts = OrderService::make($user, $company)->addProducts($order, $validated['products']);

        $usedProducts->each(
            fn ($usedProduct) => event(
                new AddedOrderProduct($user, $order, $usedProduct),
            ),
        );
    }

    protected function validate(User $user, Company $company, array $inputs): array
    {
        return Validator::make($inputs, [
            'products' => [
                'required',
                'array',
            ],
            'products.*' => [
                'required',
                'integer',
                'exists:catalog_items,id',
                function ($attribute, $value, $fail) use ($user, $company) {
                    $product = CatalogItem::findOrFail($value);

                    if (
                        !CatalogService::make($user, $company)->productOwned($product)
                    ) {
                        $fail("Company doesn't owned this product!");
                    }
                },
            ],
        ])->validate();
    }
}
