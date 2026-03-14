<?php

namespace App\Models\Filters\Json;

use App\Models\Filters\Traits\Searchable;
use App\Models\Filters\UserFilter as ModelFilter;

class PersonFilter extends ModelFilter
{
    use Searchable;
}
