<?php

namespace App\Notifications;

use App\Models\EmploymentRequest;
use App\Repositories\AvatarRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmploymentRequestAccepted extends Notification
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
    public function __construct(EmploymentRequest $request)
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
            'blade' => 'Notifications.EmploymentRequestAccepted',
            'payload' => [
                'name' => $this->request->user->full_name,
                'avatar' => (new AvatarRepository)->findModelAvatar($this->request->user),
            ],
            'router' => [
                'name' => 'contacts.addressbook',
            ],
        ];
    }
}
