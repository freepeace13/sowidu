<?php

namespace Packages\RestApi\Resources;

use App\Helpers;
use Illuminate\Http\Resources\Json\ResourceResponse as IlluminateResourceResponse;

class ResourceResponse extends IlluminateResourceResponse
{
    protected function wrap($data, $with = [], $additional = [])
    {
        return Helpers::camelCaseArrayKeys(
            parent::wrap($data, $with, $additional),
        );
    }
}
