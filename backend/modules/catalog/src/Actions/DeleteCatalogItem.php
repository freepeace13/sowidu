<?php

namespace Modules\Catalog\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Catalog\Contracts\DeletesCatalogItem as DeletesCatalogItemContract;
use Modules\Catalog\Models\CatalogItem;

class DeleteCatalogItem implements DeletesCatalogItemContract
{
    use AsAction;

    public function handle(User $user, Company $company, CatalogItem $catalogItem): bool
    {
        return $this->delete($user, $catalogItem, $company->getKey());
    }

    public function delete(User $user, CatalogItem $catalogItem, $teamId = null, $errorBag = null): bool
    {
        Gate::forUser($user)->authorize('delete', [$catalogItem, $teamId]);

        return $catalogItem->delete();
    }
}
