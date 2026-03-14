<?php

namespace Modules\Catalog\Contracts;

use App\Models\User;
use Modules\Catalog\Models\CatalogItemType;

interface CreatesCatalogItemType
{
    public function create(User $user, array $inputs, $teamId = null, $errorBag = null): CatalogItemType;
}
