<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AuthMiddleware extends HandleInertiaRequests
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => 'Auth',
            'termsConditions' => SiteSetting::app()->terms_conditions,
        ]);
    }
}
