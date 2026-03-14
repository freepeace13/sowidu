<?php

namespace App\Actions\Organization\Settings\Tax;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CreateTax
{
    use AsAction;

    public function handle(User $user, Company $company, array $inputs): Tax
    {
        Gate::forUser($user)->authorize('createTax', $company);

        $validated = $this->validate($inputs);

        return $company->taxes()
            ->create($validated);
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            'is_default' => 'boolean',
        ])->validated();
    }
}
