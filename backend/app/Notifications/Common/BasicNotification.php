<?php

namespace App\Notifications\Common;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Storage;

abstract class BasicNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $causer;

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
     * The message string format.
     *
     * @return string
     */
    abstract protected function message();

    protected function causer()
    {
        return [
            'name' => config('app.name'),
            'avatar' => Storage::disk('public')->url(config('app.logo')),
        ];
    }

    /**
     * The notification metadata.
     *
     * @return array
     */
    protected function meta()
    {
        return [];
    }

    protected function redirectTo(): ?string
    {
        return null;
    }

    protected function timeout(): int
    {
        return 5000;
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message(),
            'causer' => $this->causer(),
            'meta' => $this->meta(),
            'redirectTo' => $this->redirectTo(),
            'timeout' => $this->timeout(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
