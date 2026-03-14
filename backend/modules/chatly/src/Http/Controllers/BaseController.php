<?php

namespace Modules\Chatly\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Shared\Traits\InteractsWithImpersonator;

class BaseController extends Controller
{
    use InteractsWithImpersonator;
}
