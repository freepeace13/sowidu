<?php

namespace App\Services\Catalog;

use App\Services\PermissionService;
use Modules\Catalog\Contracts\External\PermissionContract;

class PermissionAdapter implements PermissionContract
{
    public function allows(string $permission): bool
    {
        return PermissionService::allows($permission);
    }
}
