<?php

namespace Modules\Offer\Actions\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Models\CompanyOfferConfiguration;

class SaveCompanyOfferConfiguration
{
    use AsAction;

    public function handle(Model $user, Model $company, array $inputs): CompanyOfferConfiguration
    {
        Gate::forUser($user)->authorize('updateOfferConfiguration', $company);

        $validated = $this->validate($inputs);

        return CompanyOfferConfiguration::updateOrCreate(
            ['company_id' => $company->getKey()],
            $validated,
        );
    }

    public function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'terms_and_conditions' => 'nullable|string',
        ])->validate();
    }
}
