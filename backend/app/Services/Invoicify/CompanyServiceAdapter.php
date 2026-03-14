<?php

namespace App\Services\Invoicify;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Services\CompanyService;
use Modules\Invoicify\Contracts\External\CompanyServiceContract;

class CompanyServiceAdapter implements CompanyServiceContract
{
    public function getCompanyById(int $companyId): ?Company
    {
        return Company::find($companyId);
    }

    public function getEmployeeFromUser(Company $company, User $user): Employee
    {
        return CompanyService::make($company)->getEmployeeFromUser($user);
    }
}
