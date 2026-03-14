<?php

namespace App\Contracts;

interface SearchAggregateFilter
{
    /**
     * Executes query filters
     */
    public function handle(): SearchAggregator;
}
