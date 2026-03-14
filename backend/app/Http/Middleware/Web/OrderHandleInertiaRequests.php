<?php

namespace App\Http\Middleware\Web;

use App\Enums\Permissions;
use App\Http\Controllers\Traits\WithOrderService;
use App\Http\Middleware\HandleInertiaRequests;
use App\Support\Facades\Impersonate;
use Illuminate\Http\Request;
use Sowidu\SharedData\SharedData;

class OrderHandleInertiaRequests extends HandleInertiaRequests
{
    use WithOrderService;

    public array $extraTranslations = [
        'order',
        'catalog',
        'invoices',
        'delivery_tickets',
        'work_log',
        'offer',
    ];

    /**
     * The permissions you wish to add on this Middleware
     */
    public array $permissions = [
        Permissions::CAN_ACCESS_ORDER,
        Permissions::CAN_CREATE_ORDER,
        Permissions::CAN_CONFIRM_ORDER,
        Permissions::CAN_CANCEL_ORDER,
        Permissions::CAN_ACCESS_INVOICES,
        Permissions::CAN_ACCESS_DELIVERY_TICKETS,
    ];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => 'Order',
            'defaults' => [
                'avatars' => [
                    'foreign_client' => app(SharedData::class)->get('defaults.avatars.unset'),
                    'foreign_contractor' => app(SharedData::class)->get('defaults.avatars.company'),
                ],
                'currency' => app(SharedData::class)->get('defaults.currency'),
            ],
            'requiresResponse' => [
                'outgoing' => $this->outgoingOrdersRequireResponseCount(),
                'incoming' => $this->incomingOrdersRequireResponseCount(),
            ],
        ]);
    }

    public function outgoingOrdersRequireResponseCount(): int
    {
        $service = $this->createOrderService();

        return $service
            ->outgoing()
            ->with(['client'])
            ->get()
            ->filter(fn ($order) => $service->isRequiresResponse($order))
            ->count();
    }

    public function incomingOrdersRequireResponseCount(): int
    {
        if (!Impersonate::isImpersonating()) {
            return 0;
        }

        $service = $this->createOrderService();

        return $service
            ->incoming()
            ->with(['client'])
            ->get()
            ->filter(fn ($order) => $service->isRequiresResponse($order))
            ->count();
    }
}
