<?php

namespace Modules\Shared\Models\Relations;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCategories
{
    /*public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'ownerable');
    }*/

    public function categories(): MorphMany
    {
        return $this->morphMany(
            Category::class,
            'ownerable',
        );
    }

    public function findCategoryByName(string $name)
    {
        return $this->categories()->whereName($name)->firstOrFail();
    }
}
