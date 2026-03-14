<?php

namespace Modules\Catalog\Contracts;

use App\Models\User;
use Modules\Catalog\Models\CatalogItem;

interface DeletesCatalogItem
{
    public function delete(User $user, CatalogItem $catalogItem, $teamId = null, $errorBag = null): bool;
}
