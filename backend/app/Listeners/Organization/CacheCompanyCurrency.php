<?php

namespace App\Listeners\Organization;

use App\Events\Organization\OrganizationInvoiceSettingsUpdated;
use App\Services\CacheService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheCompanyCurrency implements ShouldQueue
{
    public function handle(OrganizationInvoiceSettingsUpdated $event)
    {
        $company = $event->company;

        // Refresh the company currency cache
        $currency = $company->currency;

        CacheService::storeCompanyCurrency($company, $currency);
    }
}
