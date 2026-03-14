<?php

namespace Modules\Invoicify\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Services\InvoicePaymentService;
use Tests\TestCase;

class InvoicePaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checks_if_invoice_has_payments()
    {
        $invoice = $this->createInvoice();
        $this->createPayment($invoice, 50);

        $service = new InvoicePaymentService($invoice);

        $this->assertTrue($service->hasPayments());
    }

    public function test_checks_if_invoice_has_no_payments()
    {
        $invoice = $this->createInvoice();

        $service = new InvoicePaymentService($invoice);

        $this->assertFalse($service->hasPayments());
    }

    public function test_checks_if_invoice_is_fully_paid()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 100, 1);
        $this->createPayment($invoice, 100);

        $service = new InvoicePaymentService($invoice);

        $this->assertTrue($service->isFullyPaid());
    }

    public function test_checks_if_invoice_is_not_fully_paid()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 100, 1);
        $this->createPayment($invoice, 50);

        $service = new InvoicePaymentService($invoice);

        $this->assertFalse($service->isFullyPaid());
    }

    public function test_checks_if_invoice_is_overpaid()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 100, 1);
        $this->createPayment($invoice, 150);

        $service = new InvoicePaymentService($invoice);

        $this->assertTrue($service->isOverPaid());
    }

    public function test_checks_if_invoice_is_not_overpaid()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 100, 1);
        $this->createPayment($invoice, 50);

        $service = new InvoicePaymentService($invoice);

        $this->assertFalse($service->isOverPaid());
    }

    public function test_calculates_total_amounts_paid()
    {
        $invoice = $this->createInvoice();
        $this->createPayment($invoice, 30);
        $this->createPayment($invoice, 20);

        $service = new InvoicePaymentService($invoice);
        $totalPaid = $service->getTotalAmountsPaid();

        $this->assertEquals(50.0, $totalPaid);
    }

    protected function createInvoice()
    {
        $company = \App\Models\Company::factory()->create();

        return Invoice::factory()->create([
            'company_id' => $company->id,
        ]);
    }

    protected function createInvoiceItem(Invoice $invoice, float $price, int $quantity)
    {
        return \Modules\Invoicify\Models\InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'price' => $price,
            'quantity' => $quantity,
            'name' => 'Test Item',
        ]);
    }

    protected function createPayment(Invoice $invoice, float $amount)
    {
        return \App\Models\InvoicePayment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
        ]);
    }
}
