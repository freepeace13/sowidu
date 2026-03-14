<?php

namespace App\Http\Controllers\Inertia\Account;

use App\Http\Controllers\Inertia\InertiaController;
use App\Support\Facades\Impersonate;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Packages\Addressable\Countries;
use Packages\Addressable\Models\Address;

class AddressController extends InertiaController
{
    public function render()
    {
        $user = Impersonate::impersonator() ?? Impersonate::user();

        $addressable = $user->employer ?? $user;

        $address = $addressable->address()->newestFirst() ?? new Address;

        return Inertia::render('Account/Address', [
            'countries' => Countries::all()->pluck('name.common')->sort()->values()->toArray(),
            'address' => [
                'street' => $address->street,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
            ],
        ]);
    }

    public function states($country)
    {
        return Response::json([
            'states' => Countries::where('name.common', $country)->first()
                ->hydrateStates()->states
                ->pluck('name')
                ->sort()
                ->values()
                ->toArray(),
        ]);
    }
}
