<?php

namespace Modules\Invoicify\Actions;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Invoicify\Contracts\Actions\SendsInvoiceToClient as SendsInvoiceToClientContract;
use Modules\Invoicify\Enums\InvoiceStatus;
use Modules\Invoicify\Events\InvoiceSent;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Services\InvoiceService;

class SendToClientAction implements SendsInvoiceToClientContract
{
    use AuthorizesRequests;

    public function send($user, Invoice $invoice, $teamId = null, $errorBag = null)
    {
        $this->authorizeForUser($user, 'sendToClient', [$invoice, $teamId]);

        // Validate if invoice status is not `DRAFT`
        throw_validation_unless(
            $invoice->status->value == InvoiceStatus::DRAFT->value,
            trans('invoices.message.failed.error-sending'),
        );

        $invoiceService = new InvoiceService($invoice);

        $invoiceService->sendToClient($invoice);
        $invoiceService->savePermanentInternalId();

        event(new InvoiceSent($invoice));
    }
}
