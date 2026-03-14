<?php

namespace Packages\RestApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Packages\RestApi\Feature;

class ApiFeaturesMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $features = $request->headers->get('X-Api-Features');
        $features = explode(',', $features ?? '');

        if (in_array('PaginatedResourceCollection', $features)) {
            Feature::define('PaginatedResourceCollection', true);
        }

        if (in_array('FilteredResourceAttributes', $features)) {
            Feature::define('FilteredResourceAttributes', true);
        }

        return $next($request);
    }
}
