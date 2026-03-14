<?php

namespace App\Contracts\Actions;

use Musonza\Chat\Models\Message;

interface DeletesMessages
{
    public function delete($user, Message $message, $errorBag = null);
}
