<?php

namespace Modules\DeliveryTicket\Contracts\Services;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Modules\DeliveryTicket\Models\DeliveryTicket;

interface DeliveryTicketsServiceContract
{
    /**
     * Set the underlying query builder instance.
     *
     * @param  Builder|\Illuminate\Database\Query\Builder;  $query
     */
    public function setQuery(Builder|\Illuminate\Database\Eloquent\Builder $query): self;

    /**
     * Create a new base query.
     */
    public function newQuery(): Builder;

    /**
     * Apply filters to the query.
     */
    public function filters(array $filters = []): self;

    /**
     * Calculate total purchasing & selling prices for a DeliveryTicket.
     *
     * @return array{
     *     purchasing_price: float|int,
     *     selling_price: float|int,
     *     purchasing_price_formatted: string,
     *     selling_price_formatted: string
     * }
     */
    public function getTotalPurchasingAndSellingPrices(DeliveryTicket $deliveryTicket): array;
}
