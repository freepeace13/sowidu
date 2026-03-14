<?php

declare(strict_types=1);

namespace App\Services\WorkLogs;

use App\Models\Employee;
use App\Transformers\UserTransformer;
use Modules\WorkLogs\Contracts\External\EmployeeContract;

class EmployeeAdapter implements EmployeeContract
{
    public function getEmployeesForTeam(mixed $team, mixed $currentUser): array
    {
        return $team->employees()
            ->with('user')
            ->get()
            ->map(fn (Employee $employee) => (new UserTransformer($employee->user))
                ->withAliasName($currentUser)->resolve())
            ->toArray();
    }
}
