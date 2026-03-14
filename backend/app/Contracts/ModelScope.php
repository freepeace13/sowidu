<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface ModelScope
{
    public function apply(Builder $builder, array $parameters);
}
