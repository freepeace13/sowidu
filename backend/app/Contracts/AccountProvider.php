<?php

namespace App\Contracts;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable;

interface AccountProvider
{
    public function current(): ?Authenticatable;

    public function authorizable(): ?Authorizable;
}
