<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewTeamMember extends Notification
{
    use Queueable;

    protected $member;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Employee $member)
    {
        $this->member = $member;
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
            'role' => $this->member->role,
            'name' => $this->member->full_name,
            'photo' => $this->member->profile->avatar->getUrl(),
        ];
    }
}
