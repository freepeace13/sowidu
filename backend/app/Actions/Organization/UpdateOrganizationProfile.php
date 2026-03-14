<?php

namespace App\Actions\Organization;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateOrganizationProfile
{
    use AsAction;

    public function handle(User $user, Company $company, array $inputs): Company
    {
        Gate::forUser($user)->authorize('update', $company);

        $validated = $this->validate($inputs);

        return tap($company)
            ->update([
                'legal_form_id' => data_get($validated, 'legal_form'),
                'institution_type_id' => data_get($validated, 'institution_type'),
            ]);
    }

    protected function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'legal_form' => [
                'required',
                'integer',
                'exists:legal_forms,id',
            ],
            'institution_type' => [
                'required',
                'integer',
                'exists:institution_types,id',
            ],
        ])->validate();
    }
}
