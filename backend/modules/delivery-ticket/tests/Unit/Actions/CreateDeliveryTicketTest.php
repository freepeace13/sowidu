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
use Modules\DeliveryTicket\Actions\CreateDeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Tests\TestCase;

class CreateDeliveryTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_delivery_ticket_when_user_is_authorized(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $order = Order::factory()->create(['team_id' => $company->id]);
        $place = Place::factory()->create();
        $deliverer = Addressbook::factory()->create(['team_id' => $company->id]);

        // Set company context for OwnedByCompany validation
        Impersonate::shouldReceive('tenant')
            ->andReturn($company);

        $inputs = [
            'order' => ['id' => $order->id],
            'delivery_address' => ['id' => $place->id],
            'deliverer' => ['id' => $deliverer->id],
            'delivery_date' => now()->format('Y-m-d'),
            'external_id' => 'EXT-001',
            'type' => 1,
            'medias' => [],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('create', DeliveryTicket::class)
            ->once();

        $action = app()->make(CreateDeliveryTicket::class);
        $result = $action->handle($user, $company, $inputs);

        $this->assertInstanceOf(DeliveryTicket::class, $result);
        $this->assertEquals($company->id, $result->company_id);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($order->id, $result->order_id);
        $this->assertEquals($place->id, $result->delivery_address_id);
        $this->assertEquals('EXT-001', $result->external_id);
    }

    /** @test */
    public function it_throws_exception_when_user_is_not_authorized(): void
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();

        $inputs = [
            'order' => ['id' => 1],
            'delivery_address' => ['id' => 1],
            'delivery_date' => now()->format('Y-m-d'),
            'external_id' => 'EXT-001',
            'type' => 'incoming',
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('create', DeliveryTicket::class)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = app()->make(CreateDeliveryTicket::class);
        $action->handle($user, $company, $inputs);
    }

    /** @test */
    public function it_fails_validation_when_order_is_missing(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();

        $inputs = [
            'delivery_address' => ['id' => 1],
            'delivery_date' => now()->format('Y-m-d'),
            'external_id' => 'EXT-001',
            'type' => 'incoming',
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('create', DeliveryTicket::class)
            ->once();

        $action = app()->make(CreateDeliveryTicket::class);
        $action->handle($user, $company, $inputs);
    }

    /** @test */
    public function it_fails_validation_when_delivery_date_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();

        $inputs = [
            'order' => ['id' => 1],
            'delivery_address' => ['id' => 1],
            'delivery_date' => 'invalid-date',
            'external_id' => 'EXT-001',
            'type' => 'incoming',
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('create', DeliveryTicket::class)
            ->once();

        $action = app()->make(CreateDeliveryTicket::class);
        $action->handle($user, $company, $inputs);
    }
}
