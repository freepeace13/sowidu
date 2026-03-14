<?php

namespace App\Http\Middleware\Web;

use App\Enums\Permissions;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;

class AppSettingsInertiaRequestsHandler extends HandleInertiaRequests
{
    public array $extraTranslations = ['app_settings'];

    /**
     * The permissions you wish to add on this Middleware
     */
    public array $permissions = [];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => trans('labels.app-settings'),
        ]);
    }
}
