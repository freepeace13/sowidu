<?php

namespace Packages\ActiveStatus;

use Closure;
use Illuminate\Http\Request;

class Middleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = $this->user($request) ?: $request->user()) {
            $user->last_seen_at = now();
            $user->save();
        }

        return $next($request);
    }

    protected function user($request)
    {
        //
    }
}
