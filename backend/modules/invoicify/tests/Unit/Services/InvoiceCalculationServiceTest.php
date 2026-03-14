<?php

namespace Modules\Invoicify\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Services\InvoiceCalculationService;
use Tests\TestCase;

class InvoiceCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_subtotal()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 10, 2); // 10 * 2 = 20
        $this->createInvoiceItem($invoice, 5, 3); // 5 * 3 = 15

        $service = new InvoiceCalculationService($invoice);
        $subtotal = $service->subtotal();

        $this->assertEquals(35.0, $subtotal);
    }

    public function test_calculates_net_amount()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 100, 1);

        $service = new InvoiceCalculationService($invoice);
        $netAmount = $service->netAmount();

        // Net amount = subtotal - deductions (no deductions in this case)
        $this->assertEquals(100.0, $netAmount);
    }

    public function test_calculates_grand_total()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 100, 1);
        $tax = $this->createTax($invoice->company_id, 10); // 10% tax
        $invoice->taxes()->attach($tax);

        $service = new InvoiceCalculationService($invoice);
        $grandTotal = $service->grandTotal();

        // Grand total = net amount + taxes (100 + 10 = 110)
        $this->assertEquals(110.0, $grandTotal);
    }

    public function test_calculates_total_taxes()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 100, 1);
        $tax1 = $this->createTax($invoice->company_id, 10);
        $tax2 = $this->createTax($invoice->company_id, 5);
        $invoice->taxes()->attach([$tax1->id, $tax2->id]);

        $service = new InvoiceCalculationService($invoice);
        $totalTaxes = $service->totalTaxes();

        // Total taxes = 10% + 5% = 15% of net amount (100 * 0.15 = 15)
        $this->assertEquals(15.0, $totalTaxes);
    }

    public function test_calculates_outstanding_balance()
    {
        $invoice = $this->createInvoice();
        $this->createInvoiceItem($invoice, 100, 1);
        $this->createPayment($invoice, 30);

        $service = new InvoiceCalculationService($invoice);
        $outstandingBalance = $service->outstandingBalance();

        // Outstanding = grand total (100) - payments (30) = 70
        $this->assertEquals(70.0, $outstandingBalance);
    }

    public function test_clears_cache()
    {
        $invoice = $this->createInvoice();
        $service = new InvoiceCalculationService($invoice);

        // Calculate to populate cache
        $service->subtotal();

        // Clear cache
        $service->clearCache();

        // Cache should be cleared
        $cacheKey = 'invoice_calculation.' . $invoice->id . '.subtotal';
        $this->assertFalse(Cache::has($cacheKey));
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

    protected function createTax($companyId, float $rate)
    {
        return \App\Models\Tax::factory()->create([
            'company_id' => $companyId,
            'rate' => $rate,
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
