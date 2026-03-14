<?php

namespace Modules\Offer\Transformers;

/**
 * @property \Modules\Offer\Models\OfferItem $resource
 */
class OfferItemTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'offer_id' => $this->resource->offer_id,
            'author_id' => $this->resource->author_id,
            'name' => $this->resource->name,
            'quantity' => $this->resource->quantity,
            'quantity_formatted' => number_format($this->resource->quantity, 2),
            'price' => $this->resource->price,
            'price_formatted' => number_to_money($this->resource->price),
            'description' => $this->resource->description,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'subtotal' => $this->resource->sub_total,
            'subtotal_formatted' => number_to_money($this->resource->sub_total),
        ];
    }

    public function withDetails()
    {
        return $this->state(fn () => [
            'details' => $this->resource->details,
        ]);
    }

    public function withCurrency(string $currency): self
    {
        return $this->state(fn () => [
            'currency' => [
                'code' => $currency,
                'symbol' => currency_symbol($currency),
            ],
        ]);
    }
}
