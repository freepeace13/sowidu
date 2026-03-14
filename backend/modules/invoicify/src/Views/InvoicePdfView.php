<?php

namespace Modules\Invoicify\Views;

use Modules\Invoicify\Data\CareOf;
use Modules\Invoicify\Data\ClosingBlock;
use Modules\Invoicify\Data\InvoiceDetails;
use Modules\Invoicify\Data\ItemTable;
use Modules\Invoicify\Data\Metadata;
use Modules\Invoicify\Data\PaymentDetails;
use Modules\Invoicify\Data\Recipient;
use Modules\Invoicify\Data\Sender;
use Modules\Invoicify\Support\Pdf\ViewComponent;

class InvoicePdfView extends ViewComponent
{
    public function __construct(
        public Sender $sender,
        public Recipient $recipient,
        public CareOf $careOf,
        public InvoiceDetails $invoiceDetails,
        public ItemTable $itemTable,
        public PaymentDetails $paymentDetails,
        public ClosingBlock $closingBlock,
        public ?Metadata $metadata,
    ) {}

    /**
     * Feel free to modify this factory method where you can put additional
     * logic for retrieving data needed
     */
    public static function make(
        Sender $sender,
        Recipient $recipient,
        CareOf $careOf,
        InvoiceDetails $invoiceDetails,
        ItemTable $itemTable,
        PaymentDetails $paymentDetails,
        ClosingBlock $closingBlock,
        ?Metadata $metadata,
    ) {
        return new self(
            sender: $sender,
            recipient: $recipient,
            careOf: $careOf,
            invoiceDetails: $invoiceDetails,
            itemTable: $itemTable,
            paymentDetails: $paymentDetails,
            closingBlock: $closingBlock,
            metadata: $metadata,
        );
    }

    public function render()
    {
        return view('invoicify::components.pdf.invoice');
    }
}
