<?php

namespace App\Notifications\Common;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class AbstractNotification extends Notification
{
    use Queueable;

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

    /**
     * The message string attributes
     *
     * @return array
     */
    protected function attributes()
    {
        return [];
    }

    /**
     * The notification avatar.
     *
     * @return string
     */
    protected function avatar()
    {
        return null;
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->getMessageValue(),
            'avatar' => $this->avatar(),
            'meta' => $this->meta(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage(array_merge($this->toDatabase($notifiable), [
            'is_unread' => true,
            'alias' => (new DatabaseNotification)->getMorphClass(),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]));
    }

    /**
     * Substitute message pattern attributes and return result.
     *
     * @return string
     */
    protected function getMessageValue()
    {
        $result = $this->message();

        $attributes = Arr::wrap($this->attributes());

        foreach ($attributes as $key => $value) {
            $result = Str::replaceFirst(':' . $key, $value, $result);
        }

        return $result;
    }
}
