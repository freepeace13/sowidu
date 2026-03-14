<?php

namespace App\Listeners\Invoice;

use App\Events\Invoice\InvoiceWasFullyPaid;
use App\Mail\Invoice\ClientInvoiceMail;
use App\Models\Addressbook;
use App\Models\Company;
use App\Notifications\Invoice\ClientPaidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotifyClientForPayingInvoice implements ShouldQueue
{
    /**
     * Invoice was paid, send email to client
     *
     * @return void
     */
    public function handle(InvoiceWasFullyPaid $event)
    {
        $invoice = $event->invoice;
        $order = $invoice->order;
        $client = $order->client;

        if (same_class($client, Addressbook::class)) {
            $email = $client->email;

            if (blank($email)) {
                return;
            }

            // Client is unregistered on our app - just send email to client
            // Mail::to($email)->send(new ClientInvoiceMail($invoice, $order));

            return;
        }

        if (same_class($client, Company::class)) {
            // Client is a company
            // Fetch company owner to be notified
            $client = $invoice->order->client->founder;
        }

        // Notify client about the invoice
        Notification::send(
            $client,
            new ClientPaidNotification($invoice, $order),
        );
    }
}
