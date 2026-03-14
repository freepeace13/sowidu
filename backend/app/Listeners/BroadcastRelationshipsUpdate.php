<?php

namespace App\Listeners;

use App\Events\Contact\RelationshipUpdate;

class BroadcastRelationshipsUpdate
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        RelationshipUpdate::broadcast($event->subscriber, $event->contactable);
    }
}
