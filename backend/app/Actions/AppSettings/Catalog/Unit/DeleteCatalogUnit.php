<?php

namespace App\Actions\AppSettings\Catalog\Unit;

use App\Actions\Traits\AsAction;
use App\Models\CatalogItemUnit;
use App\Models\User;

class DeleteCatalogUnit
{
    use AsAction;

    public function handle(User $user, CatalogItemUnit $catalogItemUnit)
    {
        return $catalogItemUnit->delete();
    }
}
