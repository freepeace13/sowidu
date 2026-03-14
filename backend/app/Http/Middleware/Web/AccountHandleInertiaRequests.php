<?php

namespace App\Http\Middleware\Web;

use App\Enums\Permissions;
use App\Http\Middleware\HandleInertiaRequests;
use App\Support\Facades\Impersonate;
use App\Traits\WithOrganizationEssentials;
use Illuminate\Http\Request;

class AccountHandleInertiaRequests extends HandleInertiaRequests
{
    use WithOrganizationEssentials;

    /**
     * The permissions you wish to add on this Middleware
     */
    public array $permissions = [
        Permissions::CAN_CHANGE_AVATAR,
        Permissions::CAN_MANAGE_PERMISSIONS,
        Permissions::CAN_ADD_MEMBER,
        Permissions::CAN_MANAGE_ORGANIZATION_SETTINGS,
        Permissions::CAN_MANAGE_ORGANIZATION_CATEGORIES,
        Permissions::CAN_MANAGE_EMPLOYEE_RATES,
        Permissions::CAN_MANAGE_OFFER_CONFIGURATION,
    ];

    public array $extraTranslations = ['account'];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => 'Account',
            'categories' => $this->getCategories(Impersonate::account()),
        ]);
    }
}
