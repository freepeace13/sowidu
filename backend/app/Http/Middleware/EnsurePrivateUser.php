<?php

namespace App\Http\Middleware;

use App\Support\Facades\Impersonate;
use Closure;
use Illuminate\Http\Request;

class EnsurePrivateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Redirect user if not private
        abort_if(
            Impersonate::isImpersonating(),
            403,
            trans('validation.403'),
        );

        return $next($request);
    }
}
