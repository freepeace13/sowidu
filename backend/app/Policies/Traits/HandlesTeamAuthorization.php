<?php

namespace App\Policies\Traits;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Traits\InteractsWithImpersonator;

trait HandlesTeamAuthorization
{
    use InteractsWithImpersonator;

    protected function canRepresentTeam(User $user, ?int $teamId, string $permission)
    {
        $employee = $this->getCurrentEmployee();

        return $this->ensureUserBelongsToTeam($user, $teamId) &&
            $this->canManageForTeam($employee, $permission);
    }

    protected function canRepresentCompany(User $user, int|Company $company, string $permission): bool
    {
        $companyId = is_int($company) ? $company : $company->getKey();

        return $this->canRepresentTeam($user, $companyId, $permission);
    }

    protected function isAuthorizedTo(User $user, string $permission, $teamId = null)
    {
        $teamId = $teamId ??= $this->getCurrentTeamId();

        if (!$teamId) {
            return false;
        }

        return $this->canRepresentTeam($user, $teamId, $permission);
    }

    protected function ensureUserBelongsToTeam(User $user, ?int $teamId)
    {
        if ($teamId === null) {
            return false;
        }

        return $user->loadMissing(['teams'])
            ->teams
            ->contains(fn ($team) => $team->id == $teamId);
    }

    protected function canManageForTeam(Employee $employee, string $permission)
    {
        return $employee->can($permission);
    }

    protected function isCompanyOwner(Employee $employee, Company $company)
    {
        return $employee->is($company->getOwnersEmployeeCard());
    }
}
