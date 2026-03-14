<?php

namespace App\Models\States\OrderStates;

class DoneState extends ProgressState
{
    /**
     * The state were all order's work are done.
     */

    /**
     * @var string
     */
    public static $name = 'done';

    /**
     * {@inheritdoc}
     */
    public function color(): string
    {
        return '#039BE5';
    }
}
