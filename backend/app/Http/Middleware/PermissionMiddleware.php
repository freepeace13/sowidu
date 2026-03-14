<?php

namespace App\Http\Middleware;

use App\Support\Facades\Impersonate;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {

        abort_if(app('auth')->guest(), 403);

        if (!Impersonate::isImpersonating()) {
            return $next($request);
        }

        $authenticator = Impersonate::impersonator() ?? Impersonate::user();

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            if ($authenticator->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'User does not have the right permissions to access this page.');
    }
}
