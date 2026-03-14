<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\CatalogItem;
use App\Models\Company;
use App\Models\User;
use App\Support\Facades\Impersonate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Mockery;
use Modules\DeliveryTicket\Actions\AddMaterialToDeliveryTicket;
use Modules\DeliveryTicket\Events\DeliveryTicketMaterialsAdded;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Services\DeliveryTicketMaterialService;
use Tests\TestCase;

class AddMaterialToDeliveryTicketTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_adds_materials_to_delivery_ticket_when_user_is_authorized(): void
    {
        Event::fake([DeliveryTicketMaterialsAdded::class]);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
        ]);

        // Set company context for OwnedByCompany validation
        Impersonate::shouldReceive('tenant')
            ->andReturn($company);

        $catalogItem1 = CatalogItem::factory()->create(['company_id' => $company->id]);
        $catalogItem2 = CatalogItem::factory()->create(['company_id' => $company->id]);

        $inputs = [
            'products' => [$catalogItem1->id, $catalogItem2->id],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once();

        $serviceMock = Mockery::mock('overload:' . DeliveryTicketMaterialService::class);
        $serviceMock->shouldReceive('make')
            ->with($deliveryTicket)
            ->once()
            ->andReturnSelf();
        $serviceMock->shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();
        $serviceMock->shouldReceive('forCompany')
            ->with($company)
            ->once()
            ->andReturnSelf();
        $serviceMock->shouldReceive('addMaterials')
            ->with([$catalogItem1->id, $catalogItem2->id])
            ->once()
            ->andReturn(collect([]));

        $action = app()->make(AddMaterialToDeliveryTicket::class);
        $action->handle($user, $company, $deliveryTicket, $inputs);

        Event::assertDispatched(DeliveryTicketMaterialsAdded::class);
    }

    /** @test */
    public function it_throws_exception_when_user_is_not_authorized(): void
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
        ]);

        $inputs = [
            'products' => [1, 2],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = app()->make(AddMaterialToDeliveryTicket::class);
        $action->handle($user, $company, $deliveryTicket, $inputs);
    }

    /** @test */
    public function it_fails_validation_when_products_array_is_empty(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
        ]);

        $inputs = [
            'products' => [],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once();

        $action = app()->make(AddMaterialToDeliveryTicket::class);
        $action->handle($user, $company, $deliveryTicket, $inputs);
    }
}
