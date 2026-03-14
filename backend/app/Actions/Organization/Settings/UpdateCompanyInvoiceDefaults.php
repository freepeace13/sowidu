<?php

namespace App\Actions\Organization\Settings;

use App\Actions\Traits\AsAction;
use App\Events\Organization\OrganizationInvoiceSettingsUpdated;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Rules\OwnedByCompany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateCompanyInvoiceDefaults
{
    use AsAction;

    public function handle(User $user, Company $company, array $inputs): Company
    {
        Gate::forUser($user)->authorize('update', $company);

        $validated = $this->validate($inputs);

        $company->update(Arr::only($validated, ['currency', 'iban', 'bic', 'bank_name']));

        $company->settings()
            ->invoiceDefaults()
            ->update(
                Arr::only(
                    $validated,
                    [
                        'managing_director',
                        'website',
                        'company_email',
                        'commercial_register',
                        'commercial_register_number',
                        'payment_terms',
                        'payment_terms_instructions',
                    ],
                ),
            );

        event(new OrganizationInvoiceSettingsUpdated($company));

        return $company;
    }

    protected function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'currency' => [
                'required',
                'string',
                Rule::in(array_keys(config('app.default.currencies'))),
            ],
            'payment_terms' => 'required|integer',
            'bank_name' => 'nullable|string|min:5',
            'iban' => 'nullable|string|min:5',
            'bic' => 'nullable|string|min:5',
            'managing_director' => [
                'nullable',
                new OwnedByCompany(Employee::class),
            ],
            'website' => 'nullable|string',
            'company_email' => 'nullable|email',
            'commercial_register' => [
                'nullable',
                'string',
                Rule::in(config('app.company.commercial_registers')),
            ],
            'commercial_register_number' => 'nullable|string|min:5',
            'payment_terms_instructions' => 'nullable|string',
        ])->validate();
    }
}
