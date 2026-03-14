<?php

namespace Modules\Offer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Offer\Contracts\External\OrderServiceContract;
use Modules\Offer\Events\OfferAccepted;
use Modules\Offer\Models\Offer;

class CreateOrderFromOffer implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'high';

    public function __construct(
        protected OrderServiceContract $orderService,
    ) {}

    public function handle(OfferAccepted $event): void
    {
        $offer = $event->offer;
        $user = $event->causer;
        $company = $event->company;

        if (blank($company)) {
            // The causer is the offer recipient
            $company = $offer->company()->first();
            $user = $offer->author()->first();
        }

        if (blank($offer->order_id)) {
            // Create a new order
            $order = $this->orderService->createFromOffer($user, $offer, $company);
            $offer->update(['order_id' => $order->id]);
        }
    }

    protected function attachOfferItemsToOrder(
        Offer $offer,
        Model $order,
        Model $user,
        Model $company,
    ): void {
        $this->orderService->addItemsFromOffer($order, $offer);
    }
}
