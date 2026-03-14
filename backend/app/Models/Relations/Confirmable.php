<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Builder;

trait Confirmable
{
    /**
     * Scope a query that only include entries that are confirmed.
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('confirmed', true);
    }

    /**
     * Scope a query that only include entries that are not yet confirmed.
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnconfirm(Builder $query): Builder
    {
        return $query->where('confirmed', false);
    }

    /**
     * Determine the entry was confirmed.
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return (bool) $this->confirmed;
    }

    /**
     * Confirms the model entry.
     *
     * @return $this
     */
    public function confirm()
    {
        $this->confirmed = true;
        $this->save();

        return $this;
    }
}
