<?php

namespace App\Models\Relations;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Contractor
{
    use OwnsMedia,
        OwnsTasks;

    /**
     * Get the contractor customers
     */
    public function customers(): MorphMany
    {
        return $this->morphMany(Customer::class, 'ownerable');
    }
}
