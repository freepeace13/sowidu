<?php

namespace App\Models\QueryBuilders;

trait Excludable
{
    /**
     * Get the array of columns.
     *
     * @return array
     */
    private function getTableColumns()
    {
        return $this->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

    /**
     * @comment Exclude an array of elements from the result.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $columns
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeExclude($query, $columns)
    {
        return $query->select(array_diff($this->getTableColumns(), (array) $columns));
    }
}
