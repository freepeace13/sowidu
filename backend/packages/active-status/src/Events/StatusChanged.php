<?php

namespace Packages\ActiveStatus\Events;

class StatusChanged
{
    public $user;

    public $newStatus;

    public $prevStatus;

    public function __construct($user, $newStatus, $prevStatus)
    {
        $this->user = $user;
        $this->prevStatus = $prevStatus;
        $this->newStatus = $newStatus;
    }
}
