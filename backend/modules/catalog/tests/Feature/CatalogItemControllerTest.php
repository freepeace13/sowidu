<?php

namespace Modules\Catalog\Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Models\CatalogItemUnit;
use Tests\TestCase;

class CatalogItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

    }

    /** @test */
    public function it_displays_catalog_items_index()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->forUser($user)->create();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);

        // Create employee and ensure required permissions
        $specialization = \App\Models\Specialization::firstOrCreate([
            'title' => 'Test Specialization',
        ]);
        $employee = \App\Models\Employee::firstOrCreate(
            [
                'user_id' => $user->id,
                'company_id' => $company->id,
            ],
            [
                'specialization_id' => $specialization->id,
                'confirmed' => true,
            ],
        );
        foreach ([\Modules\Shared\Enums\Permissions::CAN_ACCESS_CATALOG, \Modules\Shared\Enums\Permissions::CAN_VIEW_SELLING_PRICE, \Modules\Shared\Enums\Permissions::CAN_VIEW_PURCHASING_PRICE] as $permName) {
            $permission = \App\Models\Permission::firstOrCreate(['name' => $permName], ['guard_name' => 'commercial']);
            $employee->givePermissionTo($permission);
        }

        CatalogItem::factory()->count(3)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('items.data', 3)
            ->has('itemTypeOptions')
            ->has('unitOptions')
            ->has('filters')
            ->has('currency'),
        );
    }

    /** @test */
    public function it_creates_catalog_item()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $unit = CatalogItemUnit::factory()->create();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);

        $data = [
            'name' => 'New Catalog Item',
            'type' => 'New Type',
            'manufacture_id' => 'MFG-001',
            'unit' => $unit->id,
            'selling_price' => 99.99,
            'description' => 'Test description',
        ];

        $response = $this->post(route('catalog.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('catalog_items', [
            'name' => 'New Catalog Item',
            'company_id' => $company->id,
        ]);
    }

    /** @test */
    public function it_updates_catalog_item()
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
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);

        $data = [
            'name' => 'Updated Name',
            'type' => 'Updated Type',
            'manufacture_id' => 'MFG-002',
            'unit' => $unit->id,
            'selling_price' => 149.99,
            'description' => 'Updated description',
        ];

        $response = $this->patch(route('catalog.update', $catalogItem), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('catalog_items', [
            'id' => $catalogItem->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function it_deletes_catalog_item()
    {
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);

        $response = $this->delete(route('catalog.destroy', $catalogItem));

        $response->assertRedirect();
        $this->assertDatabaseMissing('catalog_items', [
            'id' => $catalogItem->id,
        ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->get(route('catalog.index'));

        // Route applies permission middleware; unauthenticated results in 403
        // In some test environments, it may redirect (302) instead
        $this->assertTrue(
            in_array($response->status(), [302, 403]),
            'Unauthenticated request should be denied (expected 302 or 403, got ' . $response->status() . ')',
        );
    }
}
