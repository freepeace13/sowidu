<?php

namespace App\Models\QueryBuilders;

trait QueryHelper
{
    /**
     * WHERE $column LIKE %$value% query.
     *
     * @param  string  $boolean
     * @return mixed
     */
    public function scopeWhereLike($query, $column, $value, $boolean = 'and')
    {
        return $query->where($column, 'LIKE', "%$value%", $boolean);
    }

    /**
     * WHERE $column LIKE $value% query.
     *
     * @param  string  $boolean
     * @return mixed
     */
    public function scopeWhereBeginsWith($query, $column, $value, $boolean = 'and')
    {
        return $query->where($column, 'LIKE', "$value%", $boolean);
    }

    /**
     * WHERE $column LIKE %$value query.
     *
     * @param  string  $boolean
     * @return mixed
     */
    public function scopeWhereEndsWith($query, $column, $value, $boolean = 'and')
    {
        return $query->where($column, 'LIKE', "%$value", $boolean);
    }

    /**
     * WHERE $column LIKE %$value% query.
     *
     * @param  string  $boolean
     * @return mixed
     */
    public function scopeOrWhereLike($query, $column, $value, $boolean = 'or')
    {
        return $query->where($column, 'LIKE', "%$value%", $boolean);
    }
}
