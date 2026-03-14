<?php

namespace App\Http\Middleware\Web;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;

class TodoHandleInertiaRequests extends HandleInertiaRequests
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => 'Todo',
        ]);
    }
}
