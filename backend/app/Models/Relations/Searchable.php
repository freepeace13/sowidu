<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait Searchable
{
    /**
     * Get the searchable columns of the model
     *
     * @return array
     */
    public function getSearchableColumns()
    {
        return $this->searchable ?: [];
    }

    /**
     * Scope a query that only include passes the search text
     *
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(
        Builder $query,
        $search = null,
    ) {
        $query->when(
            filled($search),
            function ($query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $searchable = $this->getSearchableColumns();

                    $columns = Arr::get(
                        $searchable,
                        'columns',
                        [],
                    );
                    $relations = Arr::get(
                        $searchable,
                        'relations',
                        [],
                    );

                    foreach (array_merge($columns, $relations) as $attribute) {
                        $query->when(
                            method_exists(
                                $this,
                                $attribute,
                            ),
                            function (Builder $query) use ($attribute, $search) {
                                $query = $query->orWhere;

                                $closure = function (Builder $query) use ($search) {
                                    $query->search($search);
                                };

                                if (
                                    method_exists(
                                        $this->{$attribute}(),
                                        'getMorphType',
                                    )
                                ) {
                                    $query->whereHasMorph(
                                        $attribute,
                                        '*',
                                        $closure,
                                    );
                                } else {
                                    $query->orWhereHas(
                                        $attribute,
                                        $closure,
                                    );
                                }
                            },
                            function (Builder $query) use ($attribute, $search) {
                                $query->orWhere(
                                    $attribute,
                                    'LIKE',
                                    "%{$search}%",
                                );
                            },
                        );
                    }
                });
            },
        );

        return $query;
    }
}
