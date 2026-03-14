<?php

namespace App\Notifications;

use App\Models\EmploymentRequest as EmploymentRequestModel;
use App\Repositories\AvatarRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmploymentRequest extends Notification
{
    use Queueable;

    /**
     * Holds the employment_request instance
     *
     * @var App\Models\EmploymentRequest
     */
    protected $request;

    /**
     * Create a new notification instance.
     *
     * @param  App\Models\EmploymentRequest  $request
     * @return void
     */
    public function __construct(EmploymentRequestModel $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'blade' => 'Notifications.EmploymentRequest',
            'payload' => [
                'employee' => [
                    'name' => $this->request->employee->user->full_name,
                    'avatar' => (new AvatarRepository)->findModelAvatar($this->request->employee->user),
                ],
                'company' => $this->request->company->full_name,
            ],
            'router' => [
                'name' => 'employment.requests',
            ],
        ];
    }
}
