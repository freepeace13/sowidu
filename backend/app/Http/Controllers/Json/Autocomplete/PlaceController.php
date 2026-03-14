<?php

namespace App\Http\Controllers\Json\Autocomplete;

use App\Http\Controllers\Json\BaseController;
use App\Services\PlaceService;
use Illuminate\Http\Request;

class PlaceController extends BaseController
{
    protected $service;

    public function __construct(PlaceService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request)
    {
        $field = $request->query('field');
        $text = $request->query('text');
        $size = $request->query('size', 10);

        $result = $this->service->getSimilarField($field, $text, $size);

        return $this->json($result->values());
    }
}
