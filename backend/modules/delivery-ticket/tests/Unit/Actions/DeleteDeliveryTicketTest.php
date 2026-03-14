<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\DeliveryTicket\Actions\DeleteDeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Tests\TestCase;

class DeleteDeliveryTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_a_delivery_ticket_when_user_is_authorized(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('delete', $deliveryTicket)
            ->once();

        $action = new DeleteDeliveryTicket;
        $action->handle($user, $company, $deliveryTicket);

        $this->assertDatabaseMissing('delivery_tickets', [
            'id' => $deliveryTicket->id,
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

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('delete', $deliveryTicket)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = new DeleteDeliveryTicket;
        $action->handle($user, $company, $deliveryTicket);
    }
}
