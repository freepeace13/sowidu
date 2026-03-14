<?php

namespace App\Http\Controllers\Json\Autocomplete;

use App\Http\Controllers\Json\BaseController;
use App\Services\PlaceService;
use App\Transformers\PlaceTransformer;
use Illuminate\Http\Request;

class AddressController extends BaseController
{
    public function __invoke(Request $request, PlaceService $service)
    {
        return $this->json($service->distinct()
            ->getList(
                $request->only(['text', 'size']),
            )->map(
                fn ($address) => (new PlaceTransformer($address))
                    ->withId()
                    ->resolve(),
            ));
    }
}
