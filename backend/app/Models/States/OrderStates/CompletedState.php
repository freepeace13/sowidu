<?php

namespace App\Models\States\OrderStates;

class CompletedState extends ProgressState
{
    /**
     * The state were model is already filled out start waiting for
     * customer confirmation before it gets started.
     */

    /**
     * @var string
     */
    public static $name = 'completed';

    /**
     * {@inheritdoc}
     */
    public function color(): string
    {
        return '#C0CA33';
    }
}
