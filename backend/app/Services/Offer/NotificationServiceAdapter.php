<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\Common\BasicNotification;
use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Contracts\External\NotificationServiceContract;

class NotificationServiceAdapter implements NotificationServiceContract
{
    public function sendToUser(Model $user, string $title, string $message, ?string $url = null): void
    {
        /** @var User $user */
        $user->notify(new BasicNotification($title, $message, $url));
    }

    public function sendToEmployee(Model $employee, string $title, string $message, ?string $url = null): void
    {
        /** @var Employee $employee */
        $employee->notify(new BasicNotification($title, $message, $url));
    }

    public function sendOfferNotification(
        Model $notifiable,
        string $type,
        array $data,
    ): void {
        $title = $data['title'] ?? 'Offer Notification';
        $message = $data['message'] ?? '';
        $url = $data['url'] ?? null;

        $notifiable->notify(new BasicNotification($title, $message, $url));
    }
}
