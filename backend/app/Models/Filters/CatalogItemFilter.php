<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class CatalogItemFilter extends ModelFilter
{
    public function q($value)
    {
        return $this->when(
            filled($value),
            fn (Builder $query) => $query->search($value),
        );
    }

    public function type($value)
    {
        return $this->where('catalog_item_type_id', $value);
    }
}
