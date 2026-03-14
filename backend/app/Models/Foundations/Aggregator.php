<?php

namespace App\Models\Foundations;

use App\Contracts\SearchAggregator;
use App\Database\AggregateQueryBuilder;
use App\Factories\AggregateSearch as AggregateSearchFactory;
use Illuminate\Support\Traits\ForwardsCalls;

abstract class Aggregator implements SearchAggregator
{
    use ForwardsCalls;

    /**
     * @var App\Database\AggregateQueryBuilder
     */
    protected $query;

    /**
     * Create new aggregator instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->query = new AggregateQueryBuilder;

        if (property_exists($this, 'models')) {
            foreach ($this->models as $model) {
                $this->addQuery(AggregateSearchFactory::create($model));
            }
        }
    }

    /**
     * Get the aggregator filter class
     *
     * @return \App\Contracts\SearchAggregateFilter|null
     */
    public function getFilterClass()
    {
        return property_exists($this, 'filter')
            ? $this->filter
            : null;
    }

    /**
     * Perform the search query to the aggregated models.
     *
     * @return $this
     */
    public function search(string $search)
    {
        return $instance->where(
            AggregateQueryBuilder::wrapColumn('keywords'),
            'LIKE',
            "%{$search}%",
        );
    }

    /**
     * Get the aggregate query builder instance
     *
     * @return App\Database\AggregateQueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->query;
    }

    /**
     * Perform query filters to the result
     *
     * @return App\Contracts\SearchAggregateFilter
     */
    public function filter(array $inputs = [])
    {
        if (is_null($filter = $this->getFilterClass())) {
            return $this;
        }

        return (new $filter($this, $inputs))->handle();
    }

    /**
     * Handle dynamic method call into the aggregator class
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (in_array($method, ['filter', 'search'])) {
            return $this->{$method}(...$args);
        }

        return $this->forwardCallTo($this->query, $method, $args);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static;

        return (new static)->{$method}(...$args);
    }
}
