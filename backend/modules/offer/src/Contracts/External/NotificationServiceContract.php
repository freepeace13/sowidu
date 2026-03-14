<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;

/**
 * Outgoing port for notification-related services needed by the Offer module.
 * Handles sending notifications to users and employees.
 */
interface NotificationServiceContract
{
    /**
     * Send a basic notification to a user.
     */
    public function sendToUser(Model $user, string $title, string $message, ?string $url = null): void;

    /**
     * Send a basic notification to an employee.
     */
    public function sendToEmployee(Model $employee, string $title, string $message, ?string $url = null): void;

    /**
     * Send an offer-related notification.
     */
    public function sendOfferNotification(
        Model $notifiable,
        string $type,
        array $data,
    ): void;
}
