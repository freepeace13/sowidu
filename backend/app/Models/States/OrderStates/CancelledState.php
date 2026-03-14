<?php

namespace App\Models\States\OrderStates;

class CancelledState extends ProgressState
{
    /**
     * The state were model is already filled out start waiting for
     * customer confirmation before it gets started.
     */

    /**
     * @var string
     */
    public static $name = 'cancelled';

    /**
     * {@inheritdoc}
     */
    public function color(): string
    {
        return '#B71C1C';
    }
}
