<?php

namespace Modules\Chatly\Contracts;

use Musonza\Chat\Models\Conversation;

interface AddsConversationParticipants
{
    public function add($user, $joiner, Conversation $conversation, $errorBag = null);
}
