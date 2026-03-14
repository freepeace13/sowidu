<?php

namespace App\Http\Api\Controllers\V1\Public;

use App\Contracts\Place\PlaceService;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class GetCountryStatesController extends RestfulController
{
    public function __construct(
        protected PlaceService $placeService,
    ) {}

    public function __invoke(Request $request, $country)
    {
        $keyword = $request->get('keyword');

        return $this->response([
            'country' => $country,
            'states' => $this->placeService
                ->getCountryStates($country, $keyword)
                ->sort()
                ->filter()
                ->values(),
        ]);
    }
}
