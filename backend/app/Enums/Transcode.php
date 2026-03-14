<?php

namespace App\Enums;

class Transcode extends Enum
{
    const COMPLETED = 'completed';
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const FAILED = 'failed';
}
