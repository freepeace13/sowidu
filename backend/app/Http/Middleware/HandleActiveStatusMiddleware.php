<?php

namespace App\Http\Middleware;

use App\Support\Facades\Impersonate;
use Illuminate\Support\Facades\Auth;
use Packages\ActiveStatus\Middleware;

class HandleActiveStatusMiddleware extends Middleware
{
    protected function user($request)
    {
        if (Auth::check()) {
            return Impersonate::impersonator() ?? Impersonate::user();
        }
    }
}
