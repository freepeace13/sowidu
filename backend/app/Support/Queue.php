<?php

namespace App\Support;

class Queue
{
    /**
     * Queue name for emails queue
     *
     * @var string
     */
    const EMAILS = 'emails';

    /**
     * Queue name for sms queue
     *
     * @var string
     */
    const SMS = 'sms';

    /**
     * Queue name for broadcasts queue
     *
     * @var string
     */
    const BROADCAST = 'broadcasts';

    const HIGH = 'high';
    const DEFAULT = 'default';
    const LOW = 'low';
}
