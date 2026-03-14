<?php

namespace App\Contracts\Actions;

use Musonza\Chat\Models\Conversation;

interface UpdatesConversations
{
    public function update($user, Conversation $conversation, array $data, $errorBag = null);
}
