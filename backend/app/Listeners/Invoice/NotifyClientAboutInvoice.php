<?php

namespace App\Listeners\Invoice;

use App\Events\Invoice\InvoiceSent;
use App\Models\Addressbook;
use App\Models\Company;
use App\Notifications\Invoice\NewInvoiceReceivedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyClientAboutInvoice implements ShouldQueue
{
    public function handle(InvoiceSent $event)
    {
        $invoice = $event->invoice;
        $order = $invoice->order;
        $client = $order->client;

        if (same_class($client, Addressbook::class)) {
            return;
        }

        if (same_class($client, Company::class)) {
            // Client is a company
            // Fetch company owner to be notified
            $client = $invoice->order->client->founder;
        }

        // Verify if app wants to send mail to client
        if (config('app.invoice.send_mail_to_client')) {
            // Notify client about the invoice
            Notification::send(
                $client,
                new NewInvoiceReceivedNotification($invoice, $order),
            );
        }
    }
}
