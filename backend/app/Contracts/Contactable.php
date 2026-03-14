<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Contactable
{
    public function contactables(): MorphMany;
}
