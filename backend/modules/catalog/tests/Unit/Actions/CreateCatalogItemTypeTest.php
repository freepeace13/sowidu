<?php

namespace Modules\Catalog\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Actions\CreateCatalogItemType;
use Modules\Catalog\Models\CatalogItemType;
use Tests\TestCase;

class CreateCatalogItemTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_new_catalog_item_type()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $action = new CreateCatalogItemType;
        $result = $action->handle($user, $company, 'New Type');

        $this->assertInstanceOf(CatalogItemType::class, $result);
        $this->assertEquals('New Type', $result->name);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($company->id, $result->company_id);
    }

    /** @test */
    public function it_reuses_existing_catalog_item_type()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $existingType = CatalogItemType::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'name' => 'Existing Type',
        ]);

        $action = new CreateCatalogItemType;
        $result = $action->handle($user, $company, 'Existing Type');

        $this->assertEquals($existingType->id, $result->id);
        $this->assertEquals('Existing Type', $result->name);
    }

    /** @test */
    public function it_creates_different_types_for_different_companies()
    {
        $user = User::factory()->create();
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $action = new CreateCatalogItemType;
        $type1 = $action->handle($user, $company1, 'Same Name');
        $type2 = $action->handle($user, $company2, 'Same Name');

        $this->assertNotEquals($type1->id, $type2->id);
        $this->assertEquals($company1->id, $type1->company_id);
        $this->assertEquals($company2->id, $type2->company_id);
    }
}
