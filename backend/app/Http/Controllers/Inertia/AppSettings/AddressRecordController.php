<?php

namespace App\Http\Controllers\Inertia\AppSettings;

use App\Actions\AddressRecord\CreatesAddressRecord;
use App\Actions\AddressRecord\UpdateAddressRecord;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Place;
use App\Transformers\PaginatorTransformer;
use App\Transformers\PlaceTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddressRecordController extends InertiaController
{
    public function index(Request $request)
    {
        $addresses = Place::query()
            ->distinctFullAddress()
            ->when(
                $q = $request->get('q'),
                fn ($query) => $query->searchFullAddress($q),
            )
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('AppSettings/Address/Manager', [
            'addresses' => collect($addresses->items())->map(
                fn ($address) => PlaceTransformer::make($address)->withId()
                    ->resolve(),
            ),
            'pagination' => PaginatorTransformer::make($addresses),
            'filters' => $request->only(['q']),
        ]);
    }

    public function store(Request $request, CreatesAddressRecord $creator)
    {
        $creator->create($request->user(), $request->all());

        flash_success(__('app_settings.address.messages.created'));

        return back(303);
    }

    public function update(Request $request, Place $place)
    {
        UpdateAddressRecord::run(
            $request->user(),
            $place,
            $request->all(),
        );

        flash_success('Client on this order has been updated.');

        return back(303);
    }
}
