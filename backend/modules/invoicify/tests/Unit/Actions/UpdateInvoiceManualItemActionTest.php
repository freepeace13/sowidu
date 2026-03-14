<?php

namespace Modules\Invoicify\Tests\Unit\Actions;

use App\Models\CatalogItem;
use App\Models\CatalogItemUnit;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Invoicify\Actions\UpdateInvoiceManualItemAction;
use Tests\TestCase;

class UpdateInvoiceManualItemActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::define('update', fn () => true);
        Gate::before(function ($user, $ability) {
            return true;
        });
    }

    public function test_updates_manual_item_successfully()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $catalogItem = $this->createCatalogItem($company);
        $unit = $this->createUnit();

        $inputs = [
            'name' => 'Updated Manual Item',
            'type' => 'product',
            'unit' => $unit->id,
            'manufacture_id' => 'MFG-001',
            'selling_price' => 200.00,
            'description' => 'Updated description',
        ];

        $action = new UpdateInvoiceManualItemAction;
        $result = $action->update($user, $catalogItem, $inputs, $company->id);

        $this->assertInstanceOf(CatalogItem::class, $result);
        $this->assertEquals('Updated Manual Item', $result->fresh()->name);
        $this->assertEquals(200.00, $result->fresh()->selling_price);
    }

    public function test_authorizes_user_before_updating()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $catalogItem = $this->createCatalogItem($company);
        $unit = $this->createUnit();

        // Gate is already defined in setUp, so authorization should pass
        $inputs = [
            'name' => 'Updated Item',
            'type' => 'service',
            'unit' => $unit->id,
            'manufacture_id' => 'MFG-002',
            'selling_price' => 100,
            'description' => 'Description',
        ];

        $action = new UpdateInvoiceManualItemAction;
        $result = $action->update($user, $catalogItem, $inputs, $company->id);

        $this->assertInstanceOf(\App\Models\CatalogItem::class, $result);
    }

    public function test_validates_inputs_before_updating()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $catalogItem = $this->createCatalogItem($company);

        $inputs = [
            'name' => '', // Invalid: required
        ];

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action = new UpdateInvoiceManualItemAction;
        $action->update($user, $catalogItem, $inputs, $company->id);
    }

    public function test_associates_catalog_item_type_when_updating()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $catalogItem = $this->createCatalogItem($company);
        $unit = $this->createUnit();

        $inputs = [
            'name' => 'Updated Item',
            'type' => 'service',
            'unit' => $unit->id,
            'manufacture_id' => 'MFG-003',
            'selling_price' => 100,
            'description' => 'Description',
        ];

        $action = new UpdateInvoiceManualItemAction;
        $result = $action->update($user, $catalogItem, $inputs, $company->id);

        $this->assertNotNull($result->fresh()->catalog_item_type_id);
    }

    protected function createUser()
    {
        return \App\Models\User::factory()->create();
    }

    protected function createCompany()
    {
        return \App\Models\Company::factory()->create();
    }

    protected function createCatalogItem(Company $company)
    {
        $user = $this->createUser();

        // Create a minimal media record for the catalog item
        $media = \Packages\MediaLibrary\MediaCollections\Models\Media::create([
            'name' => 'Test Media',
            'file_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'disk' => 'public',
            'directory' => '',
            'model_type' => Company::class,
            'model_id' => $company->id,
            'custom_properties' => '[]',
            'generated_conversions' => '[]',
            'responsive_images' => '[]',
        ]);

        return CatalogItem::factory()
            ->state([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'media_id' => $media->id,
            ])
            ->type($user, $company)
            ->create();
    }

    protected function createUnit()
    {
        return CatalogItemUnit::factory()->create();
    }
}
