<?php

namespace Modules\Invoicify\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Models\InvoiceItem;
use Tests\TestCase;

class InvoiceItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_item_belongs_to_invoice()
    {
        $invoice = Invoice::factory()->create();
        $invoiceItem = InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
        ]);

        $this->assertInstanceOf(Invoice::class, $invoiceItem->invoice);
        $this->assertEquals($invoice->id, $invoiceItem->invoice->id);
    }

    public function test_invoice_item_calculates_subtotal()
    {
        $invoiceItem = InvoiceItem::factory()->create([
            'price' => 100.50,
            'quantity' => 2,
        ]);

        $subtotal = $invoiceItem->subtotal();

        $this->assertEquals(201.0, $subtotal);
    }
}
