<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class AddressbookFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function type($value)
    {
        return $this->when(filled($value), function ($query) use ($value) {
            $query->whereModelType($value);
        });
    }

    /**
     * Filter using the initial names
     */
    public function initial($value)
    {
        return $this->when(filled($value), fn ($query) => $query->where(
            'name',
            'LIKE',
            "$value%",
        ));
    }
}
