<?php

namespace App\Models\Search;

use App\Models\Filters\ContactableFilter;
use App\Models\Foundations\Aggregator;

class Contactable extends Aggregator
{
    protected $models = [
        \App\Models\Company::class,
        \App\Models\Employee::class,
        \App\Models\User::class,
    ];

    protected $filter = ContactableFilter::class;
}
