<?php

namespace Modules\Offer\Models\Traits;

/**
 * Helper scopes for query building.
 */
trait QueryHelper
{
    /**
     * WHERE $column LIKE %$value% query.
     */
    public function scopeWhereLike($query, string $column, string $value, string $boolean = 'and')
    {
        return $query->where($column, 'LIKE', "%$value%", $boolean);
    }

    /**
     * WHERE $column LIKE $value% query.
     */
    public function scopeWhereBeginsWith($query, string $column, string $value, string $boolean = 'and')
    {
        return $query->where($column, 'LIKE', "$value%", $boolean);
    }

    /**
     * WHERE $column LIKE %$value query.
     */
    public function scopeWhereEndsWith($query, string $column, string $value, string $boolean = 'and')
    {
        return $query->where($column, 'LIKE', "%$value", $boolean);
    }

    /**
     * WHERE $column LIKE %$value% query (OR).
     */
    public function scopeOrWhereLike($query, string $column, string $value, string $boolean = 'or')
    {
        return $query->where($column, 'LIKE', "%$value%", $boolean);
    }
}
