<?php

namespace Modules\Invoicify\Contracts\Actions;

use App\Models\CatalogItem;

interface UpdatesInvoiceManualItems
{
    public function update($user, CatalogItem $catalogItem, array $inputs, $teamId = null, $errorBag = null): CatalogItem;
}
