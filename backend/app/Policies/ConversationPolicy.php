<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;

class ConversationPolicy
{
    use HandlesAuthorization;

    public function show($user, $conversation)
    {
        return $conversation->getParticipants()->contains(function ($participant) use ($user) {
            return $participant->is($user);
        });
    }

    public function update($user, Conversation $conversation)
    {
        if ($conversation->participantFromSender($user)) {
            return true;
        }
    }

    public function showParticipants($user, Conversation $conversation)
    {
        return $conversation->getParticipants()->contains(function ($participant) use ($user) {
            return $participant->is($user);
        });
    }

    public function addParticipants($user, Conversation $conversation)
    {
        $participants = $conversation->getParticipants();

        if ($participants->contains(fn ($messageable) => $messageable->is($user))) {
            return true;
        }
    }

    public function removeParticipants($user, Conversation $conversation, Participation $participation)
    {
        $participants = $conversation->getParticipants();

        if (!$conversation->is($participation->conversation)) {
            return false;
        }

        if ($participants->contains(fn ($messageable) => $messageable->is($user))) {
            return true;
        }
    }

    public function sendMessage($user, Conversation $conversation)
    {
        return $conversation->getParticipants()->contains(function ($participant) use ($user) {
            return $participant->is($user);
        });
    }

    public function delete($user, $conversation)
    {
        return $conversation->getParticipants()->contains(function ($participant) use ($user) {
            return $participant->is($user);
        });
    }
}
