<?php

namespace App\Contracts\Business;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Contractor
{
    /**
     * Get the contractor customers
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function customers(): MorphMany;

    /**
     * Get the contractor incoming orders
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function orders(): MorphMany;

    /**
     * Get the contractor delivery tickets
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function deliveries(): MorphMany;

    /**
     * Get the contractor items
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function items(): MorphMany;
}
