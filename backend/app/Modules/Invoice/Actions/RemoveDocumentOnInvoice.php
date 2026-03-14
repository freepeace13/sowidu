<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Attachment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class RemoveDocumentOnInvoice
{
    use AsAction;

    public function handle(User $user, Invoice $invoice, Attachment $attachment)
    {
        Gate::forUser($user)->authorize('manageDocuments', $invoice);

        $attachment->delete();
    }
}
