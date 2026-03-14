<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Modules\DeliveryTicket\Actions\UpdateDeliveryTicketMaterial;
use Modules\DeliveryTicket\Events\DeliveryTicketMaterialsUpdated;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;
use Tests\TestCase;

class UpdateDeliveryTicketMaterialTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_material_quantity_when_user_is_authorized(): void
    {
        Event::fake([DeliveryTicketMaterialsUpdated::class]);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
        ]);
        $material = DeliveryTicketMaterial::factory()->create([
            'delivery_ticket_id' => $deliveryTicket->id,
            'quantity' => 5,
            'details' => collect([]),
        ]);

        $inputs = [
            'quantity' => 10,
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once();

        $action = new UpdateDeliveryTicketMaterial;
        $action->handle($user, $company, $deliveryTicket, $material, $inputs);

        $material->refresh();

        $this->assertEquals(10, $material->quantity);
        Event::assertDispatched(DeliveryTicketMaterialsUpdated::class);
    }

    /** @test */
    public function it_updates_material_prices_when_user_is_authorized(): void
    {
        Event::fake([DeliveryTicketMaterialsUpdated::class]);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
        ]);
        $material = DeliveryTicketMaterial::factory()->create([
            'delivery_ticket_id' => $deliveryTicket->id,
            'purchasing_price' => 100,
            'selling_price' => 150,
            'details' => collect([]),
        ]);

        $inputs = [
            'purchasing_price' => 120,
            'selling_price' => 180,
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once();

        $action = new UpdateDeliveryTicketMaterial;
        $action->handle($user, $company, $deliveryTicket, $material, $inputs);

        $material->refresh();

        $this->assertEquals(120, $material->purchasing_price);
        $this->assertEquals(180, $material->selling_price);
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
        $material = DeliveryTicketMaterial::factory()->create([
            'delivery_ticket_id' => $deliveryTicket->id,
        ]);

        $inputs = [
            'quantity' => 10,
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = new UpdateDeliveryTicketMaterial;
        $action->handle($user, $company, $deliveryTicket, $material, $inputs);
    }

    /** @test */
    public function it_fails_validation_when_quantity_is_negative(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
        ]);
        $material = DeliveryTicketMaterial::factory()->create([
            'delivery_ticket_id' => $deliveryTicket->id,
        ]);

        $inputs = [
            'quantity' => -5,
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once();

        $action = new UpdateDeliveryTicketMaterial;
        $action->handle($user, $company, $deliveryTicket, $material, $inputs);
    }
}
