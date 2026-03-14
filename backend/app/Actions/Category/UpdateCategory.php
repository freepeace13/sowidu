<?php

namespace App\Actions\Category;

use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateCategory
{
    public function update(
        User $user,
        Company $company,
        Category $category,
        array $inputs,
    ): Category {
        Gate::forUser($user)->authorize('update', $category);

        $validated = Validator::make($inputs, [
            'roles' => [
                'required',
                'array',
                'min:1',
            ],
            'roles.*' => [
                'required',
                'exists:roles,name',
                'min:1',
            ],
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($category, $company) {
                    if (
                        $company->categories()
                            ->get(['id', 'name'])
                            ->filter(fn ($model) => strtolower($model->name) == strtolower($value))
                            ->reject(fn ($model) => $model->is($category))
                            ->isNotEmpty()
                    ) {
                        $fail('Category name must be unique.');
                    }
                },
            ],
        ])->validate();

        $category->settings()->autoShare()->update($validated['roles']);

        return tap($category)->update(Arr::only($validated, ['name']));
    }
}
