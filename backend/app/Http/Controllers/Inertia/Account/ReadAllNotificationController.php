<?php

namespace App\Http\Controllers\Inertia\Account;

use App\Http\Controllers\Inertia\InertiaController;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;

class ReadAllNotificationController extends InertiaController
{
    use InteractsWithImpersonator;

    public function __invoke(Request $request)
    {
        $this->user()->unreadNotifications->markAsRead();

        return back(303);
    }
}
