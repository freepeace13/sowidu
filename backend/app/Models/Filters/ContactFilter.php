<?php

namespace App\Models\Filters;

use App\Registrars\AggregateSearchRegistrar;
use EloquentFilter\ModelFilter;

class ContactFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function type(string $alias)
    {
        $class = app(AggregateSearchRegistrar::class)->getModelClass($alias);

        return $this->where('contactable_type', (new $class)->getMorphClass());
    }

    public function online()
    {
        return $this->whereHasMorph('contactable', '*', function ($query) {
            return $query->online();
        });
    }
}
