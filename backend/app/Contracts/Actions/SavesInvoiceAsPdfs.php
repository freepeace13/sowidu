<?php

namespace App\Contracts\Actions;

use App\Models\User;
use Packages\Invoice\Invoice;

interface SavesInvoiceAsPdfs
{
    public function saveAsPdf(Invoice $invoice, ?User $user, $teamId = null, $errorBag = null);
}
