<?php

namespace App\Notifications;

use App\Models\Invitation;
use App\Notifications\Composers\InvitationSentComposer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class InvitationSent extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \App\Models\Invitation
     */
    protected $invitation;

    /**
     * Create a new notification instance.
     *
     * @param  string|null  $note
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
        return ['broadcast', 'database'];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $composer = new InvitationSentComposer($this->invitation);

        return [
            'title' => $composer->title(),
            'avatar' => $composer->avatar(),
            'subtitle' => $composer->subtitle(),
        ];
    }
}
