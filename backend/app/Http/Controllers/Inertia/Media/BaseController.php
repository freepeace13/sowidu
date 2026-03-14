<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Support\Facades\Impersonate;
use App\Traits\InteractsWithMedia;
use App\Traits\WithSnackbar;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use AuthorizesRequests, InteractsWithMedia, WithSnackbar;

    protected function user()
    {
        return Impersonate::impersonator() ?? Impersonate::user();
    }

    protected function shouldRespondJson()
    {
        return (bool) request()->headers->get('X-Feature-Request');
    }
}
