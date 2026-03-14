<?php

namespace Modules\Invoicify\Actions;

use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Invoicify\Contracts\Actions\MarksInvoicesAsPaid as MarksInvoicesAsPaidContract;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Services\InvoiceService;

class MarkAsPaidAction implements MarksInvoicesAsPaidContract
{
    use AuthorizesRequests;

    public function markAsPaid($user, Invoice $invoice, $teamId = null, $errorBag = null): Invoice
    {
        $this->authorizeForUser($user, 'markAsPaid', [$invoice, $teamId]);

        // Validate if invoice status is still `DRAFT`
        throw_validation_unless(
            $invoice->status->value == InvoiceStatus::SENT->value,
            trans('invoices.message.failed.error-sending'),
        );

        (new InvoiceService($invoice))->markAsPaid();

        return $invoice;
    }
}
