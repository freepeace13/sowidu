<?php

namespace Modules\Invoicify\Tests\Unit\Actions;

use App\Models\CatalogItemUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Invoicify\Actions\AddInvoiceManualItemAction;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Models\InvoiceItem;
use Modules\Invoicify\Models\InvoiceManualItem;
use Tests\TestCase;

class AddInvoiceManualItemActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::define('manageManualItems', fn () => true);
        Gate::before(function ($user, $ability) {
            return true;
        });
    }

    public function test_adds_manual_item_to_invoice_successfully()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();
        $unit = $this->createUnit();

        $inputs = [
            'name' => 'Test Manual Item',
            'type' => 'service',
            'quantity' => 2,
            'unit' => $unit->id,
            'selling_price' => 100.50,
            'description' => 'Test description',
        ];

        $action = new AddInvoiceManualItemAction;
        $result = $action->add($user, $invoice, $inputs, $company);

        $this->assertInstanceOf(InvoiceManualItem::class, $result);
        $this->assertEquals('Test Manual Item', $result->name);
        $this->assertEquals(2, $result->quantity);
        $this->assertEquals(100.50, $result->selling_price);

        $invoiceItem = \Modules\Invoicify\Models\InvoiceItem::where('invoice_id', $invoice->id)
            ->where('name', 'Test Manual Item')
            ->first();
        $this->assertNotNull($invoiceItem);
    }

    public function test_authorizes_user_before_adding_manual_item()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();
        $unit = $this->createUnit();

        // Gate is already defined in setUp, so authorization should pass
        $inputs = [
            'name' => 'Test Manual Item',
            'type' => 'service',
            'quantity' => 1,
            'unit' => $unit->id,
            'selling_price' => 100,
            'description' => 'Test description',
        ];

        $action = new AddInvoiceManualItemAction;
        $result = $action->add($user, $invoice, $inputs, $company);

        $this->assertInstanceOf(\Modules\Invoicify\Models\InvoiceManualItem::class, $result);
    }

    public function test_validates_inputs_before_adding()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();

        $inputs = [
            'name' => '', // Invalid: required
            'quantity' => -1, // Invalid: must be >= 0
        ];

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action = new AddInvoiceManualItemAction;
        $action->add($user, $invoice, $inputs, $company);
    }

    public function test_creates_invoice_item_when_adding_manual_item()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();
        $unit = $this->createUnit();

        $inputs = [
            'name' => 'Test Manual Item',
            'type' => 'service',
            'quantity' => 3,
            'unit' => $unit->id,
            'selling_price' => 150.75,
            'description' => 'Test description',
        ];

        $action = new AddInvoiceManualItemAction;
        $action->add($user, $invoice, $inputs, $company);

        $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($invoiceItem);
        $this->assertEquals('Test Manual Item', $invoiceItem->name);
        $this->assertEquals(3, $invoiceItem->quantity);
        $this->assertEquals(150.75, $invoiceItem->price);
        $this->assertEquals($user->id, $invoiceItem->user_id);
    }

    protected function createUser()
    {
        return \App\Models\User::factory()->create();
    }

    protected function createCompany()
    {
        return \App\Models\Company::factory()->create();
    }

    protected function createInvoice()
    {
        return Invoice::factory()->create();
    }

    protected function createUnit()
    {
        return CatalogItemUnit::factory()->create();
    }
}
