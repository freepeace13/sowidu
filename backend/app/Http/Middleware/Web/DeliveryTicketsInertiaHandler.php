<?php

namespace App\Http\Middleware\Web;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;

class DeliveryTicketsInertiaHandler extends HandleInertiaRequests
{
    public array $extraTranslations = ['delivery_tickets', 'catalog', 'order'];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => trans('headings.delivery_tickets'),
        ]);
    }
}
