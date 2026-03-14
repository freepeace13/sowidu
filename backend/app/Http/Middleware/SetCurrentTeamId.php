<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// use Packages\RestApi\AuthManager as Auth;

class SetCurrentTeamId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // if ($user = $request->user()) {
        //     Auth::setTeamId($user->current_team_id);
        // }

        return $next($request);
    }
}
