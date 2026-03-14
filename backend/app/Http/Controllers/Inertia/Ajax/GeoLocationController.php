<?php

namespace App\Http\Controllers\Inertia\Ajax;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Packages\Addressable\Models\Address;

class GeoLocationController extends InertiaController
{
    public function search(Request $request)
    {
        $search = $request->query('q');

        return Response::json([
            'house_numbers' => $this->getGeolocation('house_number', $search),
            'zipcodes' => $this->getGeolocation('zipcode', $search),
        ]);
    }

    protected function getGeolocation($column, $search)
    {
        return Address::select($column)
            ->distinct()
            ->when(filled($search), function ($query) use ($column, $search) {
                $query->where($column, 'like', "%{$search}%");
            })
            ->pluck($column)
            ->filter()
            ->values();
    }
}
