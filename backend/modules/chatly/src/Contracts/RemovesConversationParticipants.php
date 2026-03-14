<?php

namespace Modules\Chatly\Contracts;

use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;

interface RemovesConversationParticipants
{
    public function remove($user, Conversation $converation, Participation $participation, $errorBag = null);
}
