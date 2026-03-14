<?php

namespace App\Modules\Invoice\Services\Traits;

use App\Services\CacheService;

trait WithInvoiceSettings
{
    public function currency(): string
    {
        return CacheService::getCompanyCurrency($this->invoice->company_id);
    }
}
