<?php

namespace Modules\Invoicify\Contracts\External;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;

/**
 * Outgoing port for company-related services needed by the Invoicify module.
 */
interface CompanyServiceContract
{
    /**
     * Get a company by its ID.
     */
    public function getCompanyById(int $companyId): ?Company;

    /**
     * Get employee from user within a company context.
     */
    public function getEmployeeFromUser(Company $company, User $user): Employee;
}
