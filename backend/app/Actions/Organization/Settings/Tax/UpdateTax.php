<?php

namespace App\Actions\Organization\Settings\Tax;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateTax
{
    use AsAction;

    public function handle(User $user, Company $company, Tax $tax, array $inputs): Tax
    {
        Gate::forUser($user)->authorize('updateTax', [$company, $tax]);

        $validated = $this->validate($inputs);

        return tap($tax)->update(
            $validated,
        );
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
