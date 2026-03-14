<?php

namespace Modules\DeliveryTicket\Http\Middleware;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliveryTicketsInertiaHandler extends HandleInertiaRequests
{
    protected $rootView = 'deliveryticket::app';

    public array $extraTranslations = ['delivery_tickets', 'catalog', 'order'];

    public function share(Request $request): array
    {
        Inertia::setRootView('deliveryticket::app');

        return array_merge(parent::share($request), [
            'title' => trans('headings.delivery_tickets'),
        ]);
    }
}
