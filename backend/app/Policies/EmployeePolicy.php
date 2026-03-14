<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    public function view($user, Employee $employee, Company $company)
    {
        if ($user->belongsToTeam($company)) {
            return true;
        }
    }

    public function access($user)
    {
        return $user->exists && is_a($user, Employee::class);
    }
}
