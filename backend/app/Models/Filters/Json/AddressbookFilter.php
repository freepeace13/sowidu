<?php

namespace App\Models\Filters\Json;

use App\Models\Filters\AddressbookFilter as ModelFilter;
use App\Models\Filters\Traits\Searchable;

class AddressbookFilter extends ModelFilter
{
    use Searchable;

    public function type($value)
    {
        return $this->query->where('model_type', $value);
    }

    public function limit($value)
    {
        return $this->query->limit($value);
    }
}
