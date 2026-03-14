<?php

namespace App\Listeners\Chat;

use Chat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Musonza\Chat\Eventing\AllParticipantsClearedConversation;

class DeleteConversation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(AllParticipantsClearedConversation $event)
    {
        $conversation = $event->conversation;

        // Remove participants on this conversation first!
        $conversation->getParticipants()
            ->each(
                fn ($participant) => Chat::conversation($conversation)->removeParticipants([$participant]),
            );

        $conversation->delete(); // Delete conversation
    }
}
