<?php

namespace Modules\Catalog\Contracts;

use App\Models\User;
use Modules\Catalog\Models\CatalogItem;

interface UpdatesCatalogItem
{
    public function update(User $user, CatalogItem $catalogItem, array $inputs, $teamId = null, $errorBag = null): CatalogItem;
}
