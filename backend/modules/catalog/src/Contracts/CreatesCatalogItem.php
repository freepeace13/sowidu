<?php

namespace Modules\Catalog\Contracts;

use App\Models\User;
use Modules\Catalog\Models\CatalogItem;

interface CreatesCatalogItem
{
    public function create(User $user, array $inputs, $teamId = null, $errorBag = null): CatalogItem;
}
