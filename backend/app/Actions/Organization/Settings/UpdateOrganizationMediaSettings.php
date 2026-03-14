<?php

namespace App\Actions\Organization\Settings;

use App\Events\Organization\OrganizationMediaSettingsUpdated;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UpdateOrganizationMediaSettings
{
    public function update(User $user, Company $company, string $permission, array $inputs)
    {
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
        ])->validated();

        abort_if(blank($company), 'Please login using your organization.');

        throw_validation_unless(
            $company->settings()->media()->has($permission),
            'Please refresh the page and try again.',
        );

        $company
            ->settings()
            ->media()
            ->update($permission, $validated['roles']);

        event(new OrganizationMediaSettingsUpdated($company));
    }
}
