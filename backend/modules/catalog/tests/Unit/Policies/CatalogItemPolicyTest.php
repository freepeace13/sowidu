<?php

namespace Modules\Catalog\Tests\Unit\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Policies\CatalogItemPolicy;
use Modules\Shared\Enums\Permissions;
use Tests\TestCase;

class CatalogItemPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::policy(CatalogItem::class, CatalogItemPolicy::class);
    }

    /** @test */
    public function it_allows_user_with_permission_to_create()
    {
        $user = User::factory()->create();
        $company = Company::factory()->forUser($user)->create();
        $user->teams()->attach($company);
        $user->switchTeam($company);

        $employee = $this->createEmployee($user, $company);
        $employee->givePermissionTo($this->createPermission(Permissions::CAN_CREATE_CATALOG_ITEMS));

        $this->actingAs($user);

        $this->assertTrue($user->can('create', CatalogItem::class));
    }

    /** @test */
    public function it_denies_user_without_permission_to_create()
    {
        $user = User::factory()->create();
        $company = Company::factory()->forUser($user)->create();
        $user->teams()->attach($company);
        $user->switchTeam($company);

        $this->createEmployee($user, $company);
        // Employee doesn't have permission

        $this->actingAs($user);

        $this->assertFalse($user->can('create', CatalogItem::class));
    }

    /** @test */
    public function it_allows_user_with_permission_to_update_own_company_item()
    {
        $user = User::factory()->create();
        $company = Company::factory()->forUser($user)->create();
        $user->teams()->attach($company);
        $user->switchTeam($company);

        $employee = $this->createEmployee($user, $company);
        $employee->givePermissionTo($this->createPermission(Permissions::CAN_CREATE_CATALOG_ITEMS));

        $catalogItem = CatalogItem::factory()->create([
            'company_id' => $company->id,
        ]);

        $this->actingAs($user);

        $this->assertTrue($user->can('update', $catalogItem));
    }

    /** @test */
    public function it_denies_user_from_updating_other_company_item()
    {
        $user = User::factory()->create();
        $company1 = Company::factory()->forUser($user)->create();
        $company2 = Company::factory()->create();
        $user->teams()->attach($company1);
        $user->switchTeam($company1);

        $employee = \App\Models\Employee::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company1->id,
        ]);
        $employee->givePermissionTo($this->createPermission(Permissions::CAN_CREATE_CATALOG_ITEMS));

        $catalogItem = CatalogItem::factory()->create([
            'company_id' => $company2->id,
        ]);

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $catalogItem));
    }

    /** @test */
    public function it_allows_user_with_permission_to_delete_own_company_item()
    {
        $user = User::factory()->create();
        $company = Company::factory()->forUser($user)->create();
        $user->teams()->attach($company);
        $user->switchTeam($company);

        $employee = $this->createEmployee($user, $company);
        $employee->givePermissionTo($this->createPermission(Permissions::CAN_DELETE_CATALOG_ITEMS));

        $catalogItem = CatalogItem::factory()->create([
            'company_id' => $company->id,
        ]);

        $this->actingAs($user);

        $this->assertTrue($user->can('delete', $catalogItem));
    }

    /** @test */
    public function it_denies_user_from_deleting_other_company_item()
    {
        $user = User::factory()->create();
        $company1 = Company::factory()->forUser($user)->create();
        $company2 = Company::factory()->create();
        $user->teams()->attach($company1);
        $user->switchTeam($company1);

        $employee = \App\Models\Employee::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company1->id,
        ]);
        $employee->givePermissionTo($this->createPermission(Permissions::CAN_DELETE_CATALOG_ITEMS));

        $catalogItem = CatalogItem::factory()->create([
            'company_id' => $company2->id,
        ]);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $catalogItem));
    }

    protected function createPermission(string $name)
    {
        return \App\Models\Permission::firstOrCreate(['name' => $name]);
    }

    protected function createEmployee(User $user, Company $company)
    {
        $specialization = \App\Models\Specialization::firstOrCreate([
            'title' => 'Test Specialization',
        ]);

        return \App\Models\Employee::firstOrCreate(
            [
                'user_id' => $user->id,
                'company_id' => $company->id,
            ],
            [
                'specialization_id' => $specialization->id,
                'confirmed' => true,
            ],
        );
    }
}
