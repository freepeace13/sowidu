<?php

namespace App\Support\Category;

use App\Models\Category;

class CategorySettings
{
    public function __construct(protected Category $category) {}

    public function autoShare(): AutoShareToRoles
    {
        return new AutoShareToRoles($this->category);
    }
}
