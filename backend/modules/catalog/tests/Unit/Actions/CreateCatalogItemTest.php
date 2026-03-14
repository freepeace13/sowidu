<?php

namespace Modules\Catalog\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Modules\Catalog\Actions\CreateCatalogItem;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Models\CatalogItemType;
use Modules\Catalog\Models\CatalogItemUnit;
use Tests\TestCase;

class CreateCatalogItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

    }

    /** @test */
    public function it_creates_catalog_item_with_valid_data()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $unit = CatalogItemUnit::factory()->create();

        $inputs = [
            'name' => 'Test Item',
            'type' => 'Test Type',
            'manufacture_id' => 'MFG-001',
            'unit' => $unit->id,
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $action = new CreateCatalogItem;
        $result = $action->handle($user, $company, $inputs);

        $this->assertInstanceOf(CatalogItem::class, $result);
        $this->assertEquals('Test Item', $result->name);
        $this->assertEquals($company->id, $result->company_id);
        $this->assertEquals($user->id, $result->user_id);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $this->expectException(ValidationException::class);

        $action = new CreateCatalogItem;
        $action->handle($user, $company, []);
    }

    /** @test */
    public function it_validates_unit_exists()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $inputs = [
            'name' => 'Test Item',
            'type' => 'Test Type',
            'manufacture_id' => 'MFG-001',
            'unit' => 99999, // Non-existent unit
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $this->expectException(ValidationException::class);

        $action = new CreateCatalogItem;
        $action->handle($user, $company, $inputs);
    }

    /** @test */
    public function it_creates_catalog_item_type_when_provided()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $unit = CatalogItemUnit::factory()->create();

        $inputs = [
            'name' => 'Test Item',
            'type' => 'New Type Name',
            'manufacture_id' => 'MFG-001',
            'unit' => $unit->id,
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $action = new CreateCatalogItem;
        $result = $action->handle($user, $company, $inputs);

        $this->assertInstanceOf(CatalogItem::class, $result);
        $this->assertNotNull($result->type);
        $this->assertEquals('New Type Name', $result->type->name);
    }

    /** @test */
    public function it_reuses_existing_catalog_item_type()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $unit = CatalogItemUnit::factory()->create();
        $existingType = CatalogItemType::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'name' => 'Existing Type',
        ]);

        $inputs = [
            'name' => 'Test Item',
            'type' => 'Existing Type',
            'manufacture_id' => 'MFG-001',
            'unit' => $unit->id,
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $action = new CreateCatalogItem;
        $result = $action->handle($user, $company, $inputs);

        $this->assertInstanceOf(CatalogItem::class, $result);
        $this->assertEquals($existingType->id, $result->type->id);
    }

    /** @test */
    public function it_authorizes_user_before_creating()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $unit = CatalogItemUnit::factory()->create();

        $this->actingAs($user);

        Gate::forUser($user)->define('create', function ($user, $args) {
            return false; // Deny authorization
        });

        $inputs = [
            'name' => 'Test Item',
            'type' => 'Test Type',
            'manufacture_id' => 'MFG-001',
            'unit' => $unit->id,
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $action = new CreateCatalogItem;
        $action->handle($user, $company, $inputs);
    }

    protected function createUser()
    {
        return User::factory()->create();
    }

    protected function createCompany()
    {
        return Company::factory()->create();
    }
}
