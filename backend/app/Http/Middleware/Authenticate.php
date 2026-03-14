<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;

class Authenticate extends AuthenticateMiddleware
{
    protected function redirectTo($request)
    {
        return route('auth.login');
    }
}
