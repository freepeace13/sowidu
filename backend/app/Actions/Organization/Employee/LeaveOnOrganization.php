<?php

namespace App\Actions\Organization\Employee;

use App\Models\Company;
use App\Models\User;

class LeaveOnOrganization
{
    public function execute(User $user, Company $company)
    {
        // validate user if really employed to the org.
        throw_validation_unless($company->isEmployed($user), 'You are not employed on this organization.');

        // Remove employee to the org
        $company->currentUserEmployment->delete();
    }
}
