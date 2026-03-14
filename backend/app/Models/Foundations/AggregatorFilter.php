<?php

namespace App\Models\Foundations;

use App\Contracts\SearchAggregateFilter;
use App\Contracts\SearchAggregator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class AggregatorFilter implements SearchAggregateFilter
{
    /**
     * @var App\Contracts\SearchAggregator
     */
    protected $aggregator;

    /**
     * @var array
     */
    protected $inputs;

    /**
     * Create new aggregator filter instance.
     *
     * @param  App\Contracts\SearchAggregator  $aggregator
     * @return void
     */
    public function __construct(SearchAggregator $aggregator, array $inputs = [])
    {
        $this->aggregator = $aggregator;
        $this->inputs = $inputs;
    }

    /**
     * Execute the filter methods according to the given inputs
     *
     * @return App\Contracts\SearchAggregator
     */
    public function handle(): SearchAggregator
    {
        if (method_exists($this, 'setup')) {
            $this->setup($this->aggregator->getQueryBuilder());
        }

        foreach ($this->inputs as $key => $value) {
            $method = is_string($key) ? $key : $value;
            $method = Str::camel($method);

            $parameters = is_string($key) ? Arr::wrap($value) : [];

            if (method_exists($this, $method)) {
                $this->{$method}(
                    $this->aggregator->getQueryBuilder(),
                    ...$parameters
                );
            }
        }

        return $this->aggregator;
    }
}
