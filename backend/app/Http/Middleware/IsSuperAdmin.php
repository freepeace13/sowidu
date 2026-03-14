<?php

namespace App\Http\Middleware;

use App\Enums\Permissions;
use Closure;
use Illuminate\Http\Request;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(Permissions::isSuperAdmin($request->user()), 403);

        return $next($request);
    }
}
