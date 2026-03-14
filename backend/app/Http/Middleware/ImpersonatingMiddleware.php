<?php

namespace App\Http\Middleware;

use App\Support\Facades\Impersonate;
use Closure;
use Illuminate\Http\Request;

class ImpersonatingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        abort_if(
            !Impersonate::isImpersonating(),
            403,
            trans('validation.403'),
        );

        return $next($request);
    }
}
