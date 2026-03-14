<?php

namespace App\Models\States\OrderStates;

class PreparationState extends ProgressState
{
    /**
     * The state were all fields required to publish an order
     * to the customer for confirmation.
     */

    /**
     * @var string
     */
    public static $name = 'preparation';

    /**
     * {@inheritdoc}
     */
    public function color(): string
    {
        return '#546E7A';
    }
}
