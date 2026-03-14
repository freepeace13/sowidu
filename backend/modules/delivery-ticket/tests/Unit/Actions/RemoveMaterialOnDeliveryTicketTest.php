<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\DeliveryTicket\Actions\RemoveMaterialOnDeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;
use Tests\TestCase;

class RemoveMaterialOnDeliveryTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_removes_material_from_delivery_ticket_when_user_is_authorized(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
        ]);
        $material = DeliveryTicketMaterial::factory()->create([
            'delivery_ticket_id' => $deliveryTicket->id,
        ]);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once();

        $action = new RemoveMaterialOnDeliveryTicket;
        $action->handle($user, $company, $deliveryTicket, $material);

        $this->assertDatabaseMissing('delivery_ticket_materials', [
            'id' => $material->id,
        ]);
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

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('manageMaterials', $deliveryTicket)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = new RemoveMaterialOnDeliveryTicket;
        $action->handle($user, $company, $deliveryTicket, $material);
    }
}
