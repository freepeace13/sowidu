<?php

namespace App\Models\States\OrderStates;

class PendingState extends ProgressState
{
    /**
     * The state were order was already confirmed by the customer
     * and in "pending" state waiting for worker to process it.
     */

    /**
     * @var string
     */
    public static $name = 'pending';

    /**
     * {@inheritdoc}
     */
    public function color(): string
    {
        return '#7CB342';
    }
}
