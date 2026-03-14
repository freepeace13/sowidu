<?php

namespace Modules\Offer\Models\Relations;

use Modules\Offer\Models\Offer;

trait HasOffer
{
    public function offers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Offer::class);
    }
}
