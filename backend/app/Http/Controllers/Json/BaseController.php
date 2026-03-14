<?php

namespace App\Http\Controllers\Json;

use App\Http\Controllers\Controller;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Support\Facades\Response;

class BaseController extends Controller
{
    use InteractsWithImpersonator;

    public function json($data, $statusCode = 200)
    {
        return Response::json($data, $statusCode);
    }
}
