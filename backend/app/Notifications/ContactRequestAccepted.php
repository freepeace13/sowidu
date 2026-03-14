<?php

namespace App\Notifications;

use App\Models\ContactRequest;
use App\Repositories\AvatarRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactRequestAccepted extends Notification
{
    use Queueable;

    /**
     * Holds the contact_requests instance
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
    public function __construct(ContactRequest $request)
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
            'blade' => 'Notifications.ContactRequestAccepted',
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
