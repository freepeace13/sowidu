<?php

namespace App\Models\Filters;

use App\Database\AggregateQueryBuilder;
use App\Models\Foundations\AggregatorFilter;
use App\Registrars\AggregateSearchRegistrar;

class ContactableFilter extends AggregatorFilter
{
    /**
     * Executes first before the filter methods
     *
     * @param  App\Database\AggregateQueryBuilder  $builder
     * @return void
     */
    public function setup(AggregateQueryBuilder $builder)
    {
        $builder->mapQueries(function ($factory) {
            $factory->addSelect(
                $factory->wrapColumn('active_status'),
                'active_status',
            );

            return $factory;
        });
    }

    /**
     * Filter the result according to resource type
     *
     * @param  App\Database\AggregateQueryBuilder  $query
     * @return App\Database\AggregateQueryBuilder
     */
    public function type(AggregateQueryBuilder $query, string $alias)
    {
        return $query->where(
            AggregateQueryBuilder::wrapColumn('type'),
            app(AggregateSearchRegistrar::class)->getModelClass($alias),
        );
    }

    /**
     * Filter the result only get those online
     *
     * @param  App\Database\AggregateQueryBuilder  $query
     * @return App\Database\AggregateQueryBuilder
     */
    public function online(AggregateQueryBuilder $query)
    {
        return $query->where(
            AggregateQueryBuilder::wrapColumn('active_status'),
            'online',
        );
    }
}
