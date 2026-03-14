<?php

namespace App\Services\Order;

use App\Models\CatalogItem;
use App\Models\Company;
use App\Models\DeliveryTicketMaterial;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use App\Transformers\Catalog\CatalogItemTransformer;
use Illuminate\Support\Collection;

class OrderItemService
{
    protected User $user;

    protected Company $company;

    public function __construct(protected Order $order) {}

    public static function make(Order $order, ?User $user = null, ?Company $company = null): self
    {
        return (new self($order))->forUser($user)
            ->onCompany($company);
    }

    public function forUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function onCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function addLoneItems(array $items): Collection
    {
        $itemModels = collect($items)
            ->map(function ($product) {
                $item = CatalogItem::with(['type', 'media'])->findOrFail($product);

                return $this->itemToProduct($item);
            });

        $this->order->products()
            ->saveMany($itemModels);

        return $itemModels;
    }

    public function addProducts(array $products): Collection
    {
        $productsModels = collect($products)
            ->map(function ($product) {
                $item = CatalogItem::with(['type', 'media'])->findOrFail($product);

                return $this->itemToProduct($item);
            });

        $this->order->products()
            ->saveMany($productsModels);

        return $productsModels;
    }

    public function addDeliveryTicketMaterials(array $materials): Collection
    {
        $materialModels = collect($materials)
            ->map(function ($material) {
                return $this->materialToProduct(DeliveryTicketMaterial::findOrFail($material));
            });

        $this->order->products()
            ->saveMany($materialModels);

        return $materialModels;
    }

    public function materialToProduct(DeliveryTicketMaterial $deliveryTicketMaterial): OrderProduct
    {
        $orderProduct = new OrderProduct([
            'details' => $deliveryTicketMaterial->details,
            'quantity' => $deliveryTicketMaterial->quantity,
            'is_paid' => $deliveryTicketMaterial->is_paid,
        ]);

        $orderProduct->company()
            ->associate($this->company);
        $orderProduct->user()
            ->associate($this->user);

        $orderProduct->deliveryTicketMaterial()
            ->associate($deliveryTicketMaterial);

        if ($deliveryTicketMaterial->isPaid()) {
            $orderProduct->markAsPaid();
        }

        return $orderProduct;
    }

    public function itemToProduct(CatalogItem $item): OrderProduct
    {
        $orderProduct = new OrderProduct([
            'details' => $this->transformCatalogItemToProduct($item),
        ]);

        $orderProduct->company()
            ->associate($this->company);
        $orderProduct->user()
            ->associate($this->user);

        return $orderProduct;
    }

    public function offerItemToProduct(\Modules\Offer\Models\OfferItem $offerItem): OrderProduct
    {
        $details = $offerItem->details->when(
            $offerItem->details->has('type') === false,
            function ($details) {
                $catalogItem = CatalogItem::findOrFail($details->get('id'));

                return $details->merge([
                    'type' => [
                        'id' => $catalogItem->type->id,
                        'name' => $catalogItem->type->name,
                    ],
                ]);
            },
        )
            ->merge([
                'selling_price' => (float) $offerItem->price,
            ]);

        $orderProduct = new OrderProduct([
            'details' => $details->toArray(),
            'quantity' => $offerItem->quantity,
        ]);

        $orderProduct->offer_id = $offerItem->offer_id;

        $orderProduct->company()
            ->associate($this->company);
        $orderProduct->user()
            ->associate($this->user);

        return $orderProduct;
    }

    protected function transformCatalogItemToProduct(CatalogItem $item): array
    {
        return (new CatalogItemTransformer($item))
            ->withMedia($item->media)
            ->withType($item->type)
            ->resolve();
    }
}
