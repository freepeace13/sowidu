<?php

namespace App\Services\Invoicify;

use App\Models\User;
use App\Services\CacheService;
use Modules\Invoicify\Contracts\External\CacheServiceContract;

class CacheServiceAdapter implements CacheServiceContract
{
    public function hasUserRequestedBulkInvoiceExport(User $user): bool
    {
        return CacheService::hasUserRequestedBulkInvoiceExport($user);
    }

    public function userRequestedBulkInvoiceExport(User $user, bool $requested = true): bool
    {
        return CacheService::userRequestedBulkInvoiceExport($user, $requested);
    }

    public function userRequestedBulkInvoiceExportFinished(User $user): bool
    {
        return CacheService::userRequestedBulkInvoiceExportFinished($user);
    }

    public function getCompanyCurrency(int|\App\Models\Company $company): string
    {
        return CacheService::getCompanyCurrency($company);
    }
}
