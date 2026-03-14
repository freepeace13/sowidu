<?php

namespace App\Models\Filters\Json;

use App\Models\Filters\Traits\Searchable;
use EloquentFilter\ModelFilter;

class OrganizationFilter extends ModelFilter
{
    use Searchable;
}
