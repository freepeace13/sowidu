<?php

namespace Modules\Chatly\Contracts;

use Musonza\Chat\Models\Conversation;

interface UpdatesConversations
{
    public function update($user, Conversation $conversation, array $data, $errorBag = null);
}
