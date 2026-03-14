<?php

namespace App\Listeners\Invoice;

use App\Events\Invoice\InvoiceSent;
use App\Mail\Invoice\ClientInvoiceMail;
use App\Models\Addressbook;
use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendInvoiceMailToClient implements ShouldQueue
{
    public function handle(InvoiceSent $event)
    {
        $invoice = $event->invoice;
        $order = $invoice->order;
        $client = $order->client;

        $email = $client?->email;

        if (same_class($client, Addressbook::class)) {
            $email = $client->email;
        }

        if (same_class($client, Company::class)) {
            // Client is a company - Fetch company owner to be notified
            $email = $invoice->order->client->founder?->email;
        }

        if (blank($email)) {
            return;
        }

        // Verify if app wants to send mail to client
        if (config('app.invoice.send_mail_to_client')) {
            Mail::to($email)->send(new ClientInvoiceMail($invoice, $order)); // Send mail
        }
    }
}
