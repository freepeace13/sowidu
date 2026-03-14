<?php

namespace App\Contracts\Actions;

use Musonza\Chat\Models\Conversation;

interface AddsConversationParticipants
{
    public function add($user, $joiner, Conversation $conversation, $errorBag = null);
}
