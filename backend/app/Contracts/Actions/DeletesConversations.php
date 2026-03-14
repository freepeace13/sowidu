<?php

namespace App\Contracts\Actions;

use Musonza\Chat\Models\Conversation;

interface DeletesConversations
{
    public function delete($user, Conversation $conversation, $errorBag = null);
}
