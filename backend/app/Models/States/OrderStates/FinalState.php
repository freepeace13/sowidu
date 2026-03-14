<?php

namespace App\Models\States\OrderStates;

class FinalState extends ProgressState
{
    /**
     * The state were order processing is already started.
     */

    /**
     * @var string
     */
    public static $name = 'final';

    /**
     * {@inheritdoc}
     */
    public function color(): string
    {
        return '#43A047';
    }
}
