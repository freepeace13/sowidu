<?php

namespace App\Values;

use App\Values\Notifiable\AnonymousRecipient;

class Verifiable
{
    public $url;

    public $recipient;

    public function __construct(AnonymousRecipient $recipient, string $url)
    {
        $this->recipient = $recipient;
        $this->url = $url;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
