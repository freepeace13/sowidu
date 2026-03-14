<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;
use Modules\Shared\Enums\Permissions;
use Modules\WorkLogs\Contracts\External\InertiaMiddlewareContract;

class WorkLogInertiaRequestHandler extends Middleware
{
    protected $rootView = 'worklogs::app';

    protected array $extraTranslations = ['work_log'];

    /**
     * The permissions you wish to add on this Middleware
     */
    protected array $permissions = [
        Permissions::CAN_ACCESS_WORK_LOGS,
        Permissions::CAN_ADD_MANUAL_WORK_LOG_ENTRY,
        Permissions::CAN_ADD_MANUAL_WORK_LOG_ENTRY_FOR_OTHERS,
    ];

    protected function getInertiaMiddleware(): InertiaMiddlewareContract
    {
        return app(InertiaMiddlewareContract::class);
    }

    public function version(Request $request): ?string
    {
        return $this->getInertiaMiddleware()->getVersion($request);
    }

    public function share(Request $request): array
    {
        Inertia::setRootView('worklogs::app');

        $sharedData = $this->getInertiaMiddleware()->getSharedData(
            $request,
            $this->extraTranslations,
            $this->permissions,
        );

        return array_merge($sharedData, [
            'title' => trans('headings.work-time-logs'),
        ]);
    }
}
