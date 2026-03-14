<?php

namespace App\Notifications;

use App\Models\ContactRequest as ContactRequestModel;
use App\Models\Employee;
use App\Repositories\AvatarRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactRequest extends Notification
{
    use Queueable;

    /**
     * Holds the requestor instance
     *
     * @var App\Models\ContactRequest
     */
    protected $request;

    /**
     * Create a new notification instance.
     *
     * @param  App\Models\ContactRequest  $request
     * @return void
     */
    public function __construct(ContactRequestModel $request)
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
        $requestor = $this->request->requester;
        $account = ($requestor instanceof Employee) ? $requestor->user : $requestor;

        return [
            'blade' => 'Notifications.ContactRequest',
            'payload' => [
                'name' => $requestor instanceof Employee
                    ? $requestor->user->full_name
                    : $requestor->full_name,
                'employer' => $requestor instanceof Employee
                    ? $requestor->employer->full_name
                    : null,
                'avatar' => (new AvatarRepository)->findModelAvatar($account),
            ],
            'router' => [
                'name' => 'contacts.requests',
            ],
        ];
    }
}
