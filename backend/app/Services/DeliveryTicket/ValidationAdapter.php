<?php

namespace App\Services\DeliveryTicket;

use App\Rules\OwnedByCompany;
use Modules\DeliveryTicket\Contracts\External\ValidationContract;

class ValidationAdapter implements ValidationContract
{
    public function ownedByCompanyRule(string $modelClass, string $columnName = 'company_id'): mixed
    {
        return new OwnedByCompany($modelClass, $columnName);
    }
}
