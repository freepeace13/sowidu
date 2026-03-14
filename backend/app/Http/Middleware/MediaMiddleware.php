<?php

namespace App\Http\Middleware;

use App\Enums\MediaCategories;
use App\Enums\PermissionType;
use Illuminate\Http\Request;

class MediaMiddleware extends HandleInertiaRequests
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => 'Media Library',

            'permissionTypes' => PermissionType::getConstants(),
            'categoryTypes' => MediaCategories::getConstants(),
        ]);
    }
}
