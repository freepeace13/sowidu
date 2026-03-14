<?php

namespace App\Listeners;

class RegisterProfile
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $model = $event->user ?? $event->employee ?? $event->company;

        if ($model) {
            $model->createProfile();
        }
    }
}
