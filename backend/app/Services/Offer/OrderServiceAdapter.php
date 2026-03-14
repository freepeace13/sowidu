<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Actions\Order\Incoming\CreateIncomingOrder;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderItemService;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Contracts\External\OrderServiceContract;
use Modules\Offer\Models\Offer;

class OrderServiceAdapter implements OrderServiceContract
{
    public function __construct(
        protected CreateIncomingOrder $createIncomingOrder,
        protected OrderItemService $orderItemService,
    ) {}

    public function find(int $id): ?Model
    {
        return Order::find($id);
    }

    public function findOrFail(int $id): Model
    {
        return Order::findOrFail($id);
    }

    public function createFromOffer(Model $user, Offer $offer, Model $company): Model
    {
        /** @var User $user */
        /** @var Company $company */
        $offerToOrder = [
            'title' => $offer->title,
            'description' => $offer->description,
            'addressbook_id' => $offer->addressbook_id,
            'place_id' => $offer->place_id,
            'offer_id' => $offer->id,
        ];

        return $this->createIncomingOrder->create($user, $offerToOrder, $company);
    }

    public function addItemsFromOffer(Model $order, Offer $offer): void
    {
        /** @var Order $order */
        foreach ($offer->items as $offerItem) {
            $this->orderItemService->createFromOfferItem($order, $offerItem);
        }
    }

    public function transform(Model $order): array
    {
        /** @var Order $order */
        return (new OrderTransformer($order))->resolve();
    }

    public function transformWithOfferDetails(Model $order): array
    {
        /** @var Order $order */
        return (new OrderTransformer($order))
            ->withClientPrimaryDetails($order->client)
            ->withDeliveryAddress()
            ->withContractorPrimaryDetails($order->contractor)
            ->resolve();
    }

    public function getModelClass(): string
    {
        return Order::class;
    }
}
