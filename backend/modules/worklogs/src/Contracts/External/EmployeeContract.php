<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\External;

/**
 * Outgoing port for employee data access.
 *
 * The main application provides the Employee model adapter.
 */
interface EmployeeContract
{
    /**
     * Get employees for a team/company with user transformation.
     *
     * @param  mixed  $team  The team/company
     * @param  mixed  $currentUser  The current user (for alias naming)
     * @return array Array of transformed employee data
     */
    public function getEmployeesForTeam(mixed $team, mixed $currentUser): array;
}
