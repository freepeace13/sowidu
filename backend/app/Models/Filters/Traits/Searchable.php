<?php

namespace App\Models\Filters\Traits;

trait Searchable
{
    public function search($value)
    {
        return $this->query->search($value);
    }
}
