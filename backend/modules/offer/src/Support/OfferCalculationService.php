<?php

namespace Modules\Offer\Support;

use Modules\Offer\Models\Offer;
use Modules\Offer\Models\OfferItem;

class OfferCalculationService
{
    public function __construct(protected Offer $offer)
    {
        $this->offer->loadMissing(['items']);
    }

    public function grandTotal(): float
    {
        return $this->round($this->subtotal() + $this->totalTaxes());
    }

    public function subtotal(): float
    {
        return collect($this->offer->items)
            ->sum(fn (OfferItem $item) => $item->price * $item->quantity);
    }

    public function netTotal(): float
    {
        return $this->subtotal();
    }

    public function totalVats()
    {
        return $this->totalTaxes();
    }

    public function applyTaxRate(int|float $amount, int|float $rate): float
    {
        return $amount * ($rate / 100);
    }

    public function totalTaxes(): float
    {
        return $this->offer->properties()
            ->taxes()
            ->toCollection()
            ->all()
            ->map(fn ($tax) => $this->applyTaxRate($this->netTotal(), $tax['rate']))
            ->sum();
    }

    protected function round(float|int $number, ?int $precision = 2): float
    {
        return round($number, $precision);
    }
}
