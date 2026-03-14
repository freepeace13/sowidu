<?php

namespace Modules\Catalog\Tests\Unit\Repositories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Repositories\CatalogItemRepository;
use Tests\TestCase;

class CatalogItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_repository_instance()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $repository = new CatalogItemRepository($user, $company);

        $this->assertInstanceOf(CatalogItemRepository::class, $repository);
    }

    /** @test */
    public function it_creates_repository_with_static_make_method()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $repository = CatalogItemRepository::make($user, $company);

        $this->assertInstanceOf(CatalogItemRepository::class, $repository);
    }

    /** @test */
    public function it_filters_items_by_company()
    {
        $user = User::factory()->create();
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company1->id,
            'name' => 'Item 1',
        ]);
        CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company1->id,
            'name' => 'Item 2',
        ]);
        CatalogItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company2->id,
            'name' => 'Item 3',
        ]);

        $repository = CatalogItemRepository::make($user, $company1);
        $items = $repository->get();

        $this->assertCount(2, $items);
        $this->assertTrue($items->every(fn ($item) => $item->company_id === $company1->id));
    }

    /** @test */
    public function it_supports_method_chaining()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        CatalogItem::factory()->count(5)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $repository = CatalogItemRepository::make($user, $company);
        $items = $repository->with('type')->get();

        $this->assertCount(5, $items);
    }

    /** @test */
    public function it_supports_pagination()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        CatalogItem::factory()->count(25)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $repository = CatalogItemRepository::make($user, $company);
        $paginator = $repository->paginate(15);

        $this->assertEquals(25, $paginator->total());
        $this->assertCount(15, $paginator->items());
    }
}
