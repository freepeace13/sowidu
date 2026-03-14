<?php

namespace App\Mail\Invoice;

use App\Models\Invoice;
use App\Models\Order;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice, public Order $order) {}

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: trans('invoices.mail.send-to-client.subject', [
                'company' => $this->invoice->company->name,
                'invoice' => $this->invoice->internal_id,
            ]),

        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.invoice-to-client',
            with: [
                'totalAmount' => InvoiceService::run($this->invoice)->totalAmount(),
            ],
        );
    }
}
