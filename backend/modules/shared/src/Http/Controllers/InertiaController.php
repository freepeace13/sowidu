<?php

namespace Modules\Shared\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Inertia\LazyProp;
use Inertia\Response as InertiaResponse;
use Inertia\ResponseFactory as InertiaResponseFactory;

class InertiaController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static function render(string $component, $props = []): InertiaResponse
    {
        return static::inertia()->render($component, $props);
    }

    protected static function lazy(callable $callback): LazyProp
    {
        return static::inertia()->lazy($callback);
    }

    protected static function inertia(): InertiaResponseFactory
    {
        return app(InertiaResponseFactory::class);
    }
}
