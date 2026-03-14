<?php

namespace App\Listeners\Invoice;

use App\Enums\PaymentMethod;
use App\Events\Invoice\InvoiceWasFullyPaid;
use App\Models\InvoicePayment;
use App\Modules\Invoice\InvoiceService;

class SaveInvoicePayment
{
    public function handle(InvoiceWasFullyPaid $event)
    {
        /** @var \App\Models\Invoice $invoice */
        $invoice = $event->invoice;

        $invoiceService = InvoiceService::run($invoice);

        $totalAmount = $invoiceService->totalAmount();
        $order = $invoice->order()->with(['client'])->first();

        $clientName = $order->client?->full_name ?? $order->client->name;

        // Record invoice payment
        $invoicePayment = InvoicePayment::make([
            'payment_date' => now(),
            'amount' => $totalAmount,
            'method' => PaymentMethod::CASH,
            'payer_name' => $clientName,
        ]);

        $invoicePayment->invoice()->associate($invoice);
        $invoicePayment->save();

        // Update invoice status
        $invoiceService->updateInvoiceStatus();
    }
}
