<?php

namespace Packages\Contacts\Events;

class ContactBlocked
{
    public $sender;

    public $recipient;

    public function __construct($sender, $recipient)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
    }
}
