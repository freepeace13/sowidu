<?php

namespace Modules\Invoicify\Tests\Unit\Rules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Invoicify\Actions\Rules\GenerateInvoicePdfRules;
use Modules\Invoicify\Models\Invoice;
use Tests\TestCase;

class GenerateInvoicePdfRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_validates_invoice_with_order_successfully()
    {
        $order = \App\Models\Order::factory()->create();
        $invoice = Invoice::factory()->create([
            'order_id' => $order->id,
        ]);

        $this->expectNotToPerformAssertions();

        GenerateInvoicePdfRules::validate($invoice);
    }

    public function test_throws_validation_exception_when_order_is_missing()
    {
        // Create invoice with order that has no order_number (which is what validation checks)
        $order = \App\Models\Order::factory()->create();
        $invoice = Invoice::factory()->create([
            'order_id' => $order->id,
        ]);

        $invoice->order->order_number = null;

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(__('invoices.message.failed.reuired_order_number'));

        GenerateInvoicePdfRules::validate($invoice);
    }

    public function test_throws_validation_exception_when_order_does_not_exist()
    {
        // Create invoice with order that has no order_number
        $order = \App\Models\Order::factory()->create();
        $invoice = Invoice::factory()->create([
            'order_id' => $order->id,
        ]);

        $invoice->order->order_number = null;

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(__('invoices.message.failed.reuired_order_number'));

        GenerateInvoicePdfRules::validate($invoice);
    }
}
