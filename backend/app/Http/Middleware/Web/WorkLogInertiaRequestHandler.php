<?php

namespace App\Http\Middleware\Web;

use App\Enums\Permissions;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;

class WorkLogInertiaRequestHandler extends HandleInertiaRequests
{
    public array $extraTranslations = ['work_log'];

    /**
     * The permissions you wish to add on this Middleware
     */
    public array $permissions = [
        Permissions::CAN_ACCESS_WORK_LOGS,
        Permissions::CAN_ADD_MANUAL_WORK_LOG_ENTRY,
        Permissions::CAN_ADD_MANUAL_WORK_LOG_ENTRY_FOR_OTHERS,
    ];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => trans('headings.work-time-logs'),
        ]);
    }
}
