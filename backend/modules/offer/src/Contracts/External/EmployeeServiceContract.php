<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Outgoing port for employee-related services needed by the Offer module.
 */
interface EmployeeServiceContract
{
    /**
     * Find an employee by ID.
     */
    public function find(int $id): ?Model;

    /**
     * Find an employee by ID or fail.
     */
    public function findOrFail(int $id): Model;

    /**
     * Get employees by company.
     */
    public function getByCompany(int $companyId): Collection;

    /**
     * Get employee from user within a company context.
     */
    public function getFromUser(Model $company, Model $user): Model;
}
