<?php

namespace App\Database;

use App\Factories\AggregateSearch as Factory;
use App\Providers\SearchServiceProvider;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\ForwardsCalls;

class AggregateQueryBuilder
{
    use ForwardsCalls;

    /**
     * @var App\Database\QueryBuilder
     */
    protected $builder;

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * Create new aggregate query builder instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->builder = DB::query()->setTransformer(
            $this->getTransformer(),
        );
    }

    /**
     * Get the result transformer closure
     *
     * @return Closure
     */
    public function getTransformer()
    {
        return function ($result) {
            $model = $result->type;

            return $model::find($result->id);
        };
    }

    /**
     * Set new query factory for the given key
     *
     * @param  App\Factories\AggregateSearch  $factory
     * @return $this
     */
    public function setQuery(string $key, Factory $factory)
    {
        $this->queries[$key] = $factory;

        return $this;
    }

    /**
     * Wrap the given column name with the table name
     *
     * @return string
     */
    public static function wrapColumn(string $column)
    {
        $table = SearchServiceProvider::QUERY_NAME;

        return "{$table}.{$column}";
    }

    /**
     * Add new query (if not exists) in the sub query list
     *
     * @param  App\Factories\AggregateSearch  $subQuery
     * @return $this
     */
    public function addQuery(Factory $subQuery)
    {
        if (!$this->hasQuery($key = $subQuery->getClass())) {
            $this->setQuery($key, $subQuery);
        }

        return $this;
    }

    /**
     * Determine if the given query key is already exists
     *
     * @return bool
     */
    public function hasQuery(string $key)
    {
        return array_key_exists($key, $this->getQueries());
    }

    /**
     * Get the sub query factories
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Map the query factories through the given callback
     *
     * @return $this
     */
    public function mapQueries(Closure $callback)
    {
        foreach ($this->getQueries() as $key => $factory) {
            $this->setQuery($key, $callback($factory, $key));
        }

        return $this;
    }

    /**
     * Build sub query factories into a query builder.
     *
     * @return App\Database\QueryBuilder
     */
    protected function buildQueries()
    {
        $subQuery = null;

        foreach ($this->getQueries() as $factory) {
            if (!$subQuery) {
                $subQuery = $factory->getQuery();

                continue;
            }

            $subQuery->unionAll($factory->getQuery());
        }

        return $subQuery;
    }

    /**
     * Handle dynamic method call into the class.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['where'])) {
            $this->builder->{$method}(...$parameters);

            return $this;
        }

        if (method_exists($this, $method)) {
            return $this->{$method}(...$parameters);
        }

        $newBuilder = $this->builder->fromSub(
            $this->buildQueries(),
            SearchServiceProvider::QUERY_NAME,
        );

        return $this->forwardCallTo($newBuilder, $method, $parameters);
    }
}
