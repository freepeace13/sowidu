<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Models\Offer;

/**
 * Outgoing port for order-related services needed by the Offer module.
 * Handles Order model access and order creation from offers.
 */
interface OrderServiceContract
{
    /**
     * Find an order by ID.
     */
    public function find(int $id): ?Model;

    /**
     * Find an order by ID or fail.
     */
    public function findOrFail(int $id): Model;

    /**
     * Create an order from an accepted offer.
     */
    public function createFromOffer(Model $user, Offer $offer, Model $company): Model;

    /**
     * Add items to an order from offer items.
     */
    public function addItemsFromOffer(Model $order, Offer $offer): void;

    /**
     * Transform an order for API/frontend consumption.
     */
    public function transform(Model $order): array;

    /**
     * Transform an order with full details for offer index (client, delivery, contractor).
     */
    public function transformWithOfferDetails(Model $order): array;

    /**
     * Get the Order model class for route model binding.
     */
    public function getModelClass(): string;
}
