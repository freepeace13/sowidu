<?php

namespace App\Services\Catalog;

use App\Models\Company;
use App\Services\CompanyService;
use Modules\Catalog\Contracts\External\CompanyInfoContract;

class CompanyInfoAdapter implements CompanyInfoContract
{
    public function currency(Company $company): array
    {
        return CompanyService::make($company)->currency();
    }
}
