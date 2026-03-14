<?php

namespace Modules\Shared\Controllers;

use App\Traits\InteractsWithImpersonator;
use App\Traits\WithOrganizationEssentials;

class InertiaController extends Controller
{
    use InteractsWithImpersonator, WithOrganizationEssentials;

    /**
     * Check if request has headers `X-Feature-Request`
     * If `true` respond with JSON
     *
     * @return bool
     */
    protected function shouldRespondJson()
    {
        return (bool) request()->headers->get('X-Feature-Request');
    }
}
