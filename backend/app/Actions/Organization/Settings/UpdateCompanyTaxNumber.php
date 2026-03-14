<?php

namespace App\Actions\Organization\Settings;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateCompanyTaxNumber
{
    use AsAction;

    public function handle(User $user, Company $company, array $inputs): Company
    {
        Gate::forUser($user)->authorize('update', $company);

        $validated = $this->validate($inputs);

        return tap($company)->update([
            'tax_number' => data_get($validated, 'tax_number'),
        ]);
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'tax_number' => ['required', 'string', 'max:255'],
        ])->validate();
    }
}
