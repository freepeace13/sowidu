<?php

namespace App\Events\Invoice;

use App\Models\InvoiceItem;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceItemUpdated implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public InvoiceItem $invoiceItem)
    {
        //
    }
}
