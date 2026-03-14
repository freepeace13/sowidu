<?php

namespace App\Models\QueryBuilders\CommonScopes;

use Illuminate\Contracts\Auth\Authenticatable;

trait OwnerQueryScope
{
    public function whereOwner(Authenticatable $account)
    {
        $this->query
            ->where('ownerable_id', $account->getKey())
            ->where('ownerable_type', $account->getMorphClass());

        return $this;
    }
}
