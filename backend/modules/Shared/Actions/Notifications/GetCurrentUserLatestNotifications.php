<?php

namespace Modules\Shared\Actions\Notifications;

use App\Actions\Traits\AsAction;
use App\Models\Employee;
use App\Models\User;
use App\Transformers\NotificationTransformer;

class GetCurrentUserLatestNotifications
{
    use AsAction;

    public function handle(User|Employee $currentUser): array
    {
        return $currentUser->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(
                fn ($notification) => (new NotificationTransformer($notification))->resolve(),
            )
            ->toArray();
    }
}
