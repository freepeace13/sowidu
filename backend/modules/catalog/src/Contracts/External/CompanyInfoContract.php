<?php

namespace Modules\Catalog\Contracts\External;

use App\Models\Company;

/**
 * Outgoing port for retrieving company-related information
 * needed by the Catalog module.
 */
interface CompanyInfoContract
{
    /**
     * Get the company's currency info (name and symbol).
     *
     * @return array{name: string, symbol: string}
     */
    public function currency(Company $company): array;
}
