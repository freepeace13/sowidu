<?php

namespace App\Http\Middleware\Web;

use App\Enums\Permissions;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;

class CatalogHandleInertiaRequests extends HandleInertiaRequests
{
    public array $extraTranslations = ['catalog'];

    /**
     * The permissions you wish to add on this Middleware
     */
    public array $permissions = [
        Permissions::CAN_VIEW_PURCHASING_PRICE,
        Permissions::CAN_VIEW_SELLING_PRICE,
        Permissions::CAN_CREATE_CATALOG_ITEMS,
        Permissions::CAN_DELETE_CATALOG_ITEMS,
    ];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => trans('headings.catalogue'),
        ]);
    }
}
