<?php

namespace App\Http\Controllers\Json\Place;

use App\Http\Controllers\Json\BaseController;
use App\Services\PlaceService;
use Illuminate\Http\Request;

class CountryController extends BaseController
{
    protected $service;

    public function __construct(PlaceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->json(
            $this->service
                ->getCountries($request->get('keyword')),
        );
    }

    public function states(Request $request, $country)
    {
        return $this->json(
            $this->service
                ->getCountryStates($country, $request->get('keyword'))
                ->sort()
                ->filter()
                ->values(),
        );
    }

    public function cities(Request $request, $country)
    {
        return $this->json(
            $this->service
                ->getCountryCities($country, $request->get('keyword'))
                ->sort()
                ->values(),
        );
    }
}
