<?php

namespace App\Contracts;

interface NotificationComposer
{
    /**
     * @return string
     */
    public function avatar();

    /**
     * @return string
     */
    public function title();

    /**
     * @return string
     */
    public function subtitle();
}
