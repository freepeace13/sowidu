<?php

namespace App\Notifications\Organization;

use App\Models\Company;
use App\Models\Employee;
use App\Transformers\UserTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * @todo Add email notification
 */
class NotifyAdminOnEmployeeLeaved extends Notification
{
    use Queueable;

    public function __construct(public Company $company, public Employee $employee) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $causer = $this->employee->user;

        return [
            'message' => __('notifications.organization.admin.employee.leaved', [
                'employee' => $causer->full_name,
            ]),
            'causer' => (new UserTransformer($causer))->resolve(),
        ];
    }
}
