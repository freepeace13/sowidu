<?php

namespace Packages\ActiveStatus;

use Packages\ActiveStatus\Events\StatusChanged;

trait HasActiveStatus
{
    public function isStatus(string $status): bool
    {
        return $this->active_status == $status;
    }

    public function switchStatus(string $status): self
    {
        $prevStatus = $this->active_status;

        $this->active_status = $status;
        $this->save();

        event(new StatusChanged($this, $status, $prevStatus));

        return $this;
    }
}
