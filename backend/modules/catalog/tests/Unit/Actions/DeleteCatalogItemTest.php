<?php

namespace Modules\Catalog\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Catalog\Actions\DeleteCatalogItem;
use Modules\Catalog\Models\CatalogItem;
use Tests\TestCase;

class DeleteCatalogItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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

        $action = new DeleteCatalogItem;
        $result = $action->handle($user, $company, $catalogItem);

        $this->assertTrue($result);
        $this->assertNull(CatalogItem::find($catalogItem->id));
    }

    /** @test */
    public function it_authorizes_user_before_deleting()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $catalogItem = CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $this->actingAs($user);

        Gate::forUser($user)->define('delete', function ($user, $args) {
            return false; // Deny authorization
        });

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $action = new DeleteCatalogItem;
        $action->handle($user, $company, $catalogItem);
    }
}
