<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class SetDefaultGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach (config('auth.guards') as $guard => $value) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);
            }
        }

        return $next($request);
    }
}
