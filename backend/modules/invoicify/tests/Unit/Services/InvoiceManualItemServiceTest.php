<?php

namespace Modules\Invoicify\Tests\Unit\Services;

use App\Models\CatalogItemUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoicify\Models\InvoiceManualItem;
use Modules\Invoicify\Services\InvoiceManualItemService;
use Tests\TestCase;

class InvoiceManualItemServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_manual_item_successfully()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $unit = $this->createUnit();

        $data = [
            'name' => 'Test Manual Item',
            'type' => 'service',
            'quantity' => 2,
            'unit' => $unit->id,
            'selling_price' => 100.50,
            'description' => 'Test description',
        ];

        $service = InvoiceManualItemService::make($user, $company);
        $result = $service->create($data);

        $this->assertInstanceOf(InvoiceManualItem::class, $result);
        $this->assertEquals('Test Manual Item', $result->name);
        $this->assertEquals(2, $result->quantity);
        $this->assertEquals(100.50, $result->selling_price);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($company->id, $result->company_id);
        $this->assertNotNull($result->vendor_id);
        $this->assertNotNull($result->internal_id);
        $this->assertStringStartsWith('MI-', $result->internal_id);
    }

    public function test_deletes_manual_item_successfully()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $manualItem = InvoiceManualItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $service = InvoiceManualItemService::make($user, $company);
        $service->delete($manualItem->id);

        $this->assertDatabaseMissing('invoice_manual_items', [
            'id' => $manualItem->id,
        ]);
    }

    public function test_throws_exception_when_deleting_item_from_different_company()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $otherCompany = $this->createCompany();
        $manualItem = InvoiceManualItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $otherCompany->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invoice manual item not found');

        $service = InvoiceManualItemService::make($user, $company);
        $service->delete($manualItem->id);
    }

    public function test_generates_vendor_id()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $unit = $this->createUnit();

        $data = [
            'name' => 'Test Item',
            'type' => 'service',
            'quantity' => 1,
            'unit' => $unit->id,
            'selling_price' => 100,
            'description' => 'Description',
        ];

        $service = InvoiceManualItemService::make($user, $company);
        $result = $service->create($data);

        $this->assertNotNull($result->vendor_id);
        $this->assertNotEmpty($result->vendor_id);
    }

    public function test_sets_unit_name_from_unit()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $unit = CatalogItemUnit::factory()->create(['name' => 'Piece']);

        $data = [
            'name' => 'Test Item',
            'type' => 'service',
            'quantity' => 1,
            'unit' => $unit->id,
            'selling_price' => 100,
            'description' => 'Description',
        ];

        $service = InvoiceManualItemService::make($user, $company);
        $result = $service->create($data);

        $this->assertEquals('Piece', $result->unit_name);
    }

    protected function createUser()
    {
        return \App\Models\User::factory()->create();
    }

    protected function createCompany()
    {
        return \App\Models\Company::factory()->create();
    }

    protected function createUnit()
    {
        return CatalogItemUnit::factory()->create();
    }
}
