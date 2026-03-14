<?php

namespace App\Notifications;

use App\Models\CompanyInvitation as Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewCompanyInvitation extends Notification
{
    use Queueable;

    protected $invitation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toDatabase($notifiable)
    {
        return [
            'name' => $this->invitation->company->name,
            'photo' => $this->invitation->company->profile->avatar->getUrl(),
        ];
    }
}
