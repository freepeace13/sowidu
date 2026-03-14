<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Order;
use App\Models\Place;
use App\Models\User;
use App\Support\Facades\Impersonate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Modules\DeliveryTicket\Actions\UpdateDeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Tests\TestCase;

class UpdateDeliveryTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_a_delivery_ticket_when_user_is_authorized(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);

        $newOrder = Order::factory()->create(['team_id' => $company->id]);
        $newPlace = Place::factory()->create();
        $newDeliverer = Addressbook::factory()->create(['team_id' => $company->id]);

        // Set company context for OwnedByCompany validation
        Impersonate::shouldReceive('tenant')
            ->andReturn($company);

        $inputs = [
            'order' => ['id' => $newOrder->id],
            'delivery_address' => ['id' => $newPlace->id],
            'deliverer' => ['id' => $newDeliverer->id],
            'delivery_date' => now()->addDay()->format('Y-m-d'),
            'external_id' => 'EXT-UPDATED',
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $deliveryTicket)
            ->once();

        $action = app()->make(UpdateDeliveryTicket::class);
        $action->handle($user, $company, $deliveryTicket, $inputs);

        $deliveryTicket->refresh();

        $this->assertEquals($newOrder->id, $deliveryTicket->order_id);
        $this->assertEquals($newPlace->id, $deliveryTicket->delivery_address_id);
        $this->assertEquals($newDeliverer->id, $deliveryTicket->deliverer_id);
        $this->assertEquals('EXT-UPDATED', $deliveryTicket->external_id);
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
            'order' => ['id' => 1],
            'delivery_address' => ['id' => 1],
            'deliverer' => ['id' => 1],
            'delivery_date' => now()->format('Y-m-d'),
            'external_id' => 'EXT-001',
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $deliveryTicket)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = app()->make(UpdateDeliveryTicket::class);
        $action->handle($user, $company, $deliveryTicket, $inputs);
    }

    /** @test */
    public function it_fails_validation_when_delivery_date_is_missing(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
        ]);

        $inputs = [
            'order' => ['id' => 1],
            'delivery_address' => ['id' => 1],
            'deliverer' => ['id' => 1],
            'external_id' => 'EXT-001',
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $deliveryTicket)
            ->once();

        $action = app()->make(UpdateDeliveryTicket::class);
        $action->handle($user, $company, $deliveryTicket, $inputs);
    }
}
