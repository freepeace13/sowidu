<?php

namespace App\Actions\Invoice\ManualItem;

use App\Actions\Traits\AsAction;
use App\Models\CatalogItem;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class RemoveInvoiceManualItem
{
    use AsAction;

    public function handle(User $user, Company $company, CatalogItem $catalogItem)
    {
        Gate::forUser($user)->authorize('delete', $catalogItem);

        return $catalogItem->delete();
    }
}
