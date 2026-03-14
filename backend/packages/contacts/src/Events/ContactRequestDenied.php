<?php

namespace Packages\Contacts\Events;

use Packages\Contacts\Models\Contactship;

class ContactRequestDenied
{
    public $contactship;

    public function __construct(Contactship $contactship)
    {
        $this->contactship = $contactship;
    }
}
