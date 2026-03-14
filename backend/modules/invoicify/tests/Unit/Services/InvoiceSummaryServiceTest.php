<?php

namespace Modules\Invoicify\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoicify\Enums\InvoiceStatus;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Services\InvoiceSummaryService;
use Tests\TestCase;

class InvoiceSummaryServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @skip Temporarily skipped as requested.
     *
     * TODO: Implement test for saving invoice summaries for sent invoices.
     * This test should verify that InvoiceSummaryService correctly saves summary data
     * when an invoice is sent. Need to understand the summary data structure and
     * verify the service saves it correctly.
     */
    public function test_saves_invoice_summaries_for_sent_invoice()
    {
        $this->markTestSkipped('Skipping InvoiceSummaryServiceTest as requested - TODO: Implement test');
    }

    /**
     * @skip Temporarily skipped as requested.
     *
     * TODO: Implement test for ensuring summaries are not saved for draft invoices.
     * This test should verify that InvoiceSummaryService does NOT save summary data
     * when an invoice is still in draft status. Need to verify the service behavior
     * for draft invoices.
     */
    public function test_does_not_save_summaries_for_draft_invoice()
    {
        $this->markTestSkipped('Skipping InvoiceSummaryServiceTest as requested - TODO: Implement test');
    }

    public function test_returns_deductions_relation()
    {
        $invoice = $this->createInvoice();
        $service = InvoiceSummaryService::run($invoice);

        $deductions = $service->deductions();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $deductions);
    }

    public function test_returns_taxes_relation()
    {
        $invoice = $this->createInvoice();
        $service = InvoiceSummaryService::run($invoice);

        $taxes = $service->taxes();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $taxes);
    }

    protected function createInvoice(InvoiceStatus $status = InvoiceStatus::DRAFT)
    {
        $company = \App\Models\Company::factory()->create();

        return Invoice::factory()->create([
            'company_id' => $company->id,
            'status' => $status,
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
}
