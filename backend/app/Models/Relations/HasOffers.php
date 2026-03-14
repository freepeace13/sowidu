<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Offer\Models\CompanyOfferConfiguration;
use Modules\Offer\Models\Offer;

trait HasOffers
{
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function offerConfiguration(): HasOne
    {
        return $this->hasOne(CompanyOfferConfiguration::class)->withDefault([
            'terms_and_conditions' => null,
        ]);
    }
}
