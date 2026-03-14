<?php

namespace Packages\Contacts\Events;

class ContactUnblocked
{
    public $sender;

    public $recipient;

    public function __construct($sender, $recipient)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
    }
}
