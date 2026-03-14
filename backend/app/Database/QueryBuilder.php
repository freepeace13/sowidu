<?php

namespace App\Database;

use Closure;
use Illuminate\Database\Query\Builder;

class QueryBuilder extends Builder
{
    /**
     * @var bool
     */
    protected $transformer;

    /**
     * Set the query to handle results from multiple tables.
     *
     * @return $this
     */
    public function setTransformer(Closure $transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Get the table name of query
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->from;
    }

    /**
     * Determine the query aggregates multiple models.
     *
     * @return bool
     */
    public function getTransformer()
    {
        return $this->transformer ?: function ($result) {
            return $result;
        };
    }

    /**
     * {@inheritdoc}
     */
    public function get($columns = ['*'])
    {
        return parent::get($columns)->transform(
            $this->getTransformer(),
        );
    }
}
