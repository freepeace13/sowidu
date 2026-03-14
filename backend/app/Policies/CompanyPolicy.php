<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\Company;
use App\Models\Tax;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use Company as Repository;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class CompanyPolicy
{
    use HandlesAuthorization;
    use HandlesTeamAuthorization;

    public function impersonate(User $user, Company $company)
    {
        return $company->isVerifiedEmployee($user);
    }

    public function login(Authorizable $user, Company $company)
    {
        return Repository::hasEmployeeUser($company, $user);
    }

    public function updateAddress($user, $company)
    {
        if ($user->ownsTeam($company)) {
            return true;
        }
    }

    public function update(User $user, $company)
    {
        return $user->ownsCompany($company)
            || $this->canRepresentCompany(
                $user,
                $company,
                Permissions::CAN_UPDATE_SETTINGS,
            );
    }

    public function manageEmployeeRates(User $user, Company $company)
    {
        if ($user->ownsTeam($company)) {
            return true;
        }

        return $user->ownsCompany($company) || $this->canRepresentCompany($user, $company, Permissions::CAN_MANAGE_EMPLOYEE_RATES);

    }

    public function createTax(User $user, Company $company)
    {
        return $user->ownsCompany($company) || $this->canRepresentCompany($user, $company, Permissions::CAN_MANAGE_TAX);
    }

    public function updateTax(User $user, Company $company, Tax $tax)
    {
        return $user->ownsCompany($company) || $this->canRepresentCompany($user, $company, Permissions::CAN_MANAGE_TAX) && $tax->company()
            ->is($company);
    }

    public function deleteTax(User $user, Company $company, Tax $tax)
    {
        return $user->ownsCompany($company) || $this->canRepresentCompany($user, $company, Permissions::CAN_MANAGE_TAX) && $tax->company()
            ->is($company);
    }

    public function updateOfferConfiguration(User $user, Company $company)
    {
        return $user->ownsCompany($company) || $this->canRepresentCompany($user, $company, Permissions::CAN_MANAGE_OFFER_CONFIGURATION);
    }
}
