<?php

namespace Modules\Catalog\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Modules\Catalog\Actions\UpdateCatalogItem;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Models\CatalogItemUnit;
use Tests\TestCase;

class UpdateCatalogItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

    }

    /** @test */
    public function it_updates_catalog_item_with_valid_data()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $unit = CatalogItemUnit::factory()->create();
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'name' => 'Original Name',
        ]);

        $inputs = [
            'name' => 'Updated Name',
            'type' => 'Updated Type',
            'manufacture_id' => 'MFG-002',
            'unit' => $unit->id,
            'selling_price' => 149.99,
            'description' => 'Updated description',
        ];

        $action = new UpdateCatalogItem;
        $result = $action->handle($user, $company, $catalogItem, $inputs);

        $this->assertInstanceOf(CatalogItem::class, $result);
        $this->assertEquals('Updated Name', $result->fresh()->name);
        $this->assertEquals('Updated description', $result->fresh()->description);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $this->expectException(ValidationException::class);

        $action = new UpdateCatalogItem;
        $action->handle($user, $company, $catalogItem, []);
    }

    /** @test */
    public function it_updates_catalog_item_type()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $unit = CatalogItemUnit::factory()->create();
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'name' => 'Test Item',
            'type' => 'New Type',
            'manufacture_id' => 'MFG-001',
            'unit' => $unit->id,
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $action = new UpdateCatalogItem;
        $result = $action->handle($user, $company, $catalogItem, $inputs);

        $this->assertNotNull($result->fresh()->type);
        $this->assertEquals('New Type', $result->fresh()->type->name);
    }

    /** @test */
    public function it_updates_unit_and_unit_name()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $unit1 = CatalogItemUnit::factory()->create(['name' => 'Unit 1']);
        $unit2 = CatalogItemUnit::factory()->create(['name' => 'Unit 2']);
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'unit' => $unit1->id,
            'unit_name' => 'Unit 1',
        ]);

        $inputs = [
            'name' => 'Test Item',
            'type' => 'Test Type',
            'manufacture_id' => 'MFG-001',
            'unit' => $unit2->id,
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $action = new UpdateCatalogItem;
        $result = $action->handle($user, $company, $catalogItem, $inputs);

        $this->assertEquals($unit2->id, $result->fresh()->unit);
        $this->assertEquals('Unit 2', $result->fresh()->unit_name);
    }

    /** @test */
    public function it_authorizes_user_before_updating()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);
        $unit = CatalogItemUnit::factory()->create();

        $this->actingAs($user);

        Gate::forUser($user)->define('update', function ($user, $args) {
            return false; // Deny authorization
        });

        $inputs = [
            'name' => 'Updated Name',
            'type' => 'Test Type',
            'manufacture_id' => 'MFG-001',
            'unit' => $unit->id,
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $action = new UpdateCatalogItem;
        $action->handle($user, $company, $catalogItem, $inputs);
    }
}
