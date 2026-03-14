<?php

namespace App\Notifications;

use App\Http\Resources\UserResource;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AcceptedInvitation extends Notification
{
    use Queueable;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $recipient;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Model $recipient, $type = null)
    {
        $this->recipient = $recipient;
        $this->type = $type;
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
        return [
            'type' => $this->type,
            'recipient' => (new UserResource($this->recipient))->resolve(),
        ];
    }
}
