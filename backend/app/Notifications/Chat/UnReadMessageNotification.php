<?php

namespace App\Notifications\Chat;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\MessageNotification;
use Musonza\Chat\Models\Participation;

class UnReadMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Conversation $conversation;

    public function __construct(
        protected MessageNotification $messageNotification,
    ) {
        $participation = Participation::with([
            'messageable',
            'conversation',
        ])->find($messageNotification->participation_id);

        $this->conversation = $participation->conversation;

        $messageNotification->markAsRead();
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
            'message' => __('chat.notifications.message.unread'),
            'redirectTo' => route('chatly.show', ['id' => $this->conversation->id]),
        ];
    }
}
