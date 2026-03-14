<?php

namespace App\Http\Api\Controllers\V1;

use App\Enums\PermissionGroup;
use Packages\RestApi\RestfulController;

class GetPermissionsController extends RestfulController
{
    public function __invoke()
    {
        return PermissionGroup::all();
    }
}
