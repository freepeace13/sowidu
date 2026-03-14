<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class ItemFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    /**
     * Filter the results according to item type.
     */
    public function type(string $type)
    {
        return $this->where('item_type_id', $type);
    }
}
