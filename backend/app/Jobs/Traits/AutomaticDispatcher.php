<?php

namespace App\Jobs\Traits;

trait AutomaticDispatcher
{
    protected function redispatch($job): void
    {
        $this->autoDispatch($job);
    }

    protected function autoDispatch($job): void
    {
        if ($this->batch()) {
            $this->batch()
                ->add($job);
        } else {
            dispatch($job);
        }
    }
}
