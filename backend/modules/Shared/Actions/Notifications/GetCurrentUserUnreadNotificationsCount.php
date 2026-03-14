<?php

namespace Modules\Shared\Actions\Notifications;

use App\Actions\Traits\AsAction;
use App\Models\Employee;
use App\Models\User;

class GetCurrentUserUnreadNotificationsCount
{
    use AsAction;

    public function handle(User|Employee $currentUser): int
    {
        return $currentUser->unreadNotifications()->count();
    }
}
