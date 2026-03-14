<?php

namespace App\Http\Controllers\Inertia\Addressbook;

use App\Actions\AddressRecord\CreatesAddressRecord;
use App\Http\Controllers\Inertia\InertiaController;
use App\Transformers\PlaceTransformer;
use Illuminate\Http\Request;

class StoreNewAddressController extends InertiaController
{
    public function __invoke(Request $request, CreatesAddressRecord $creator)
    {
        $place = $creator->create($request->user(), $request->all());

        flash_data((new PlaceTransformer($place))->withId()->resolve());

        return back(303);

    }
}
