<?php

namespace Modules\Chatly\Contracts;

use Musonza\Chat\Models\Conversation;

interface DeletesConversations
{
    public function delete($user, Conversation $conversation, $errorBag = null);
}
