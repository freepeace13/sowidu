<?php

namespace Modules\Offer\Middleware;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfferInertiaRequestHandler extends HandleInertiaRequests
{
    protected $rootView = 'offer::app';

    public array $extraTranslations = ['offer', 'catalog'];

    public array $permissions = [
        'can manage offers',
    ];

    public function share(Request $request): array
    {
        Inertia::setRootView('offer::app');

        return array_merge(parent::share($request), [
            'title' => trans('headings.offers'),
        ]);
    }
}
