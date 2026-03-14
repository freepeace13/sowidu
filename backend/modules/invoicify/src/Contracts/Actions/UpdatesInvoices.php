<?php

namespace Modules\Invoicify\Contracts\Actions;

use App\Models\User;
use Modules\Invoicify\Models\Invoice;

interface UpdatesInvoices
{
    public function update(User $user, Invoice $invoice, array $inputs, $teamId = null, $errorBag = null);
}
