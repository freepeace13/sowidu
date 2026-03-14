<?php

namespace Modules\Offer\Support;

use Modules\Offer\Models\Offer;

class OfferProperties
{
    public function __construct(protected Offer $offer) {}

    public function taxes(): OfferTaxProperty
    {
        return new OfferTaxProperty($this->offer);
    }
}
