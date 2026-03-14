<?php

namespace Modules\Invoicify\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoicify\Enums\InvoiceKind;
use Modules\Invoicify\Enums\InvoiceStatus;
use Modules\Invoicify\Enums\InvoiceType;
use Modules\Invoicify\Models\Invoice;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_has_correct_casts()
    {
        $invoice = Invoice::factory()->create([
            'type' => InvoiceType::OUTGOING,
            'status' => InvoiceStatus::DRAFT,
            'kind' => InvoiceKind::PARTIAL_1,
        ]);

        $this->assertInstanceOf(InvoiceType::class, $invoice->type);
        $this->assertInstanceOf(InvoiceStatus::class, $invoice->status);
        $this->assertInstanceOf(InvoiceKind::class, $invoice->kind);
    }

    public function test_invoice_casts_dates_correctly()
    {
        $invoice = Invoice::factory()->create([
            'delivery_date' => '2024-01-15',
            'send_date' => '2024-01-20',
            'payment_date' => '2024-02-01',
        ]);

        $this->assertEquals('2024-01-15', $invoice->delivery_date->format('Y-m-d'));
        $this->assertEquals('2024-01-20', $invoice->send_date->format('Y-m-d'));
        $this->assertEquals('2024-02-01', $invoice->payment_date->format('Y-m-d'));
    }

    public function test_invoice_casts_collections_correctly()
    {
        $invoice = Invoice::factory()->create([
            'biller_details' => ['name' => 'Test Company'],
            'final_data' => ['key' => 'value'],
            'preview_layout' => ['layout' => 'default'],
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $invoice->biller_details);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $invoice->final_data);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $invoice->preview_layout);
    }
}
