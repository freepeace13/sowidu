<?php

namespace Packages\Contacts\Events;

class ContactRequestCancelled
{
    public $recipient;

    public $sender;

    public function __construct($sender, $recipient)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
    }
}
