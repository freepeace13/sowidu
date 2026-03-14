<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Models\Company;
use App\Models\User;

class SaveDefaultCategoriesToAccount
{
    public function save(User|Company $company)
    {
        $defaultCategories = config('app.default.categories');
        $company->categories()
            ->saveMany(
                array_map(
                    fn ($category) => new Category([
                        'name' => $category,
                        'is_default' => true,
                    ]),
                    $defaultCategories,
                ),
            );
    }
}
