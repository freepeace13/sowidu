<?php

namespace App\Http\Middleware;

use App\Exceptions\UnknownEmployeeException;
use Closure;

class EnsureValidEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $company = $request->company();

        if (!$company) {
            throw new UnknownEmployeeException('You are not employee of the company you are trying to access.');
        }

        return $next($request);
    }
}
