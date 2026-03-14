<?php

namespace App\Actions\Category;

use App\Events\Category\CategoryCreated;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CreateCategory
{
    public function create(User $user, Company $company, array $inputs): Category
    {
        Gate::forUser($user)->authorize('create', Category::class);

        $validated = Validator::make($inputs, [
            'name' => [
                'required',
                function ($attribute, $value, $fail) use ($company) {
                    if ($company->categories()->whereName($value)->exists()) {
                        $fail(trans('validation.unique', compact('attribute')));
                    }
                },
            ],
        ])->validate();

        $category = $company->categories()->save(Category::create($validated));

        event(new CategoryCreated($category, $user));

        return $category;
    }
}
