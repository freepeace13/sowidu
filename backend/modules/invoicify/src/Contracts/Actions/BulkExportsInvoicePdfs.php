<?php

namespace Modules\Invoicify\Contracts\Actions;

use App\Models\User;

interface BulkExportsInvoicePdfs
{
    public function handle(User $user, array $inputs, $teamId = null, $errorBag = null);
}
