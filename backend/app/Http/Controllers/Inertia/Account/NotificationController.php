<?php

namespace App\Http\Controllers\Inertia\Account;

use App\Http\Controllers\Inertia\InertiaController;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification as Notification;

class NotificationController extends InertiaController
{
    public function index(Request $request)
    {
        return response()->json(
            $this->user()
                ->notifications()
                ->latest()
                ->simplePaginate(20)
                ->through(fn ($notification) => (new NotificationTransformer($notification))->resolve()),
        );
    }

    public function read(Notification $notification)
    {
        $notification->markAsRead();

        return back(303);
    }
}
