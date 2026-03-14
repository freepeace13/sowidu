<?php

namespace App\Contracts\Actions;

use App\Models\User;

interface RetrievesIncomingOrders
{
    public function retrieve(User $user, array $filters, string $sortBy = 'DESC', int $perPage = 20, $teamId = null);
}
