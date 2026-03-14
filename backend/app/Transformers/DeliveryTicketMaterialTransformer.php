<?php

namespace App\Transformers;

/**
 * @property \App\Models\DeliveryTicketMaterial $resource
 */
class DeliveryTicketMaterialTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'delivery_ticket_id' => $this->resource->delivery_ticket_id,
            'quantity' => $this->resource->quantity,
            'details' => $this->resource->details,
            'is_paid' => $this->resource->is_paid,
        ];
    }

    public function withDeliveryTicket()
    {
        return $this->state(fn () => [
            'delivery_ticket' => (new DeliveryTicketTransformer($this->resource->deliveryTicket))
                ->resolve(),
        ]);
    }

    public function withPurchasingPrice(string $currency): self
    {
        return $this->state(fn () => [
            'purchasing_price' => $this->resource->purchasing_price,
            'purchasing_price_formatted' => number_to_money(
                $this->resource->purchasing_price,
                $currency,
            ),
        ]);
    }

    public function withSellingPrice(string $currency): self
    {
        return $this->state(fn () => [
            'selling_price' => $this->resource->selling_price,
            'selling_price_formatted' => number_to_money(
                $this->resource->selling_price,
                $currency,
            ),
        ]);
    }

    public function withCurrency($currency): self
    {
        return $this->state(fn () => [
            'currency' => $currency,
        ]);
    }
}
