<?php

namespace Modules\Catalog\Tests\Unit\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Models\CatalogItemType;
use Modules\Catalog\Models\CatalogItemUnit;
use Modules\Catalog\Services\CatalogService;
use Tests\TestCase;

class CatalogServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_all_item_types_for_company()
    {
        $user = User::factory()->create();
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        CatalogItemType::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company1->id,
            'name' => 'Type 1',
        ]);
        CatalogItemType::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company1->id,
            'name' => 'Type 2',
        ]);
        CatalogItemType::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company2->id,
            'name' => 'Other Company Type',
        ]);

        $service = CatalogService::make($user, $company1);
        $types = $service->allItemTypes();

        $this->assertCount(2, $types);
        $this->assertTrue($types->every(fn ($type) => $type->company_id === $company1->id));
    }

    /** @test */
    public function it_updates_vendor_and_internal_ids()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'vendor_id' => null,
            'internal_id' => null,
        ]);

        $service = CatalogService::make($user, $company);
        $service->updateVendorAndInternalIds($catalogItem);

        $catalogItem->refresh();
        $this->assertNotNull($catalogItem->vendor_id);
        $this->assertNotNull($catalogItem->internal_id);
        $this->assertStringStartsWith('SW-', $catalogItem->internal_id);
    }

    /** @test */
    public function it_creates_catalog_item()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $type = CatalogItemType::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);
        $unit = CatalogItemUnit::factory()->create();

        $payload = [
            'name' => 'New Item',
            'vendor_id' => null,
            'internal_id' => null,
            'unit' => $unit->id,
            'unit_name' => $unit->name,
            'manufacture_id' => 'MFG-001',
            'purchasing_price' => 50.00,
            'selling_price' => 99.99,
            'description' => 'Test item',
        ];

        $service = CatalogService::make($user, $company);
        $item = $service->createItem($type, $payload);

        $this->assertInstanceOf(CatalogItem::class, $item);
        $this->assertEquals('New Item', $item->name);
        $this->assertEquals($type->id, $item->catalog_item_type_id);
        $this->assertEquals($user->id, $item->user_id);
        $this->assertEquals($company->id, $item->company_id);
        $this->assertEquals($unit->name, $item->unit_name);
        $this->assertNotNull($item->vendor_id);
        $this->assertNotNull($item->internal_id);
    }

    /** @test */
    public function it_checks_if_product_is_owned_by_company()
    {
        $user = User::factory()->create();
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company1->id,
        ]);

        $service1 = CatalogService::make($user, $company1);
        $service2 = CatalogService::make($user, $company2);

        $this->assertTrue($service1->productOwned($catalogItem));
        $this->assertFalse($service2->productOwned($catalogItem));
    }
}
