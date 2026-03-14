<?php

namespace App\Http\Api\Controllers\V1\Public;

use App\Contracts\Place\PlaceService;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class GetCountryListController extends RestfulController
{
    public function __construct(
        protected PlaceService $placeService,
    ) {}

    public function __invoke(Request $request)
    {
        $keyword = $request->get('keyword');

        return $this->response([
            'countries' => $this->placeService->getCountries($keyword),
        ]);
    }
}
