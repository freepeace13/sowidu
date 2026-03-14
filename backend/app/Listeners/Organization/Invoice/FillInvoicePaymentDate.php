<?php

namespace App\Listeners\Organization\Invoice;

use App\Events\Organization\OrganizationInvoiceSettingsUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class FillInvoicePaymentDate implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(OrganizationInvoiceSettingsUpdated $event)
    {
        $company = $event->company;

        // Get payment_terms
        $paymentTerms = $company->settings()
            ->invoiceDefaults()
            ->get('payment_terms');

        if (blank($paymentTerms) || !is_numeric($paymentTerms)) {
            return;
        }

        // Fill the payment date on invoices
        $company->invoices()
            ->whereNull('payment_date')
            ->get()
            ->each(function ($invoice) use ($paymentTerms) {
                $dateCreated = $invoice->created_at;

                $invoice->update([
                    'payment_date' => $dateCreated->addDays($paymentTerms),
                ]);
            });
    }
}
