<?php

namespace App\Services\DeliveryTicket;

use App\Models\Company;
use App\Services\CompanyService;
use Modules\DeliveryTicket\Contracts\External\CompanyContract;

class CompanyAdapter implements CompanyContract
{
    public function getCurrency(mixed $company): string
    {
        return CompanyService::make($company)->currency()['name'];
    }

    public function getEmployees(mixed $company): mixed
    {
        return $company->employees ?? collect();
    }

    public function find(int $id): mixed
    {
        return Company::findOrFail($id);
    }
}
