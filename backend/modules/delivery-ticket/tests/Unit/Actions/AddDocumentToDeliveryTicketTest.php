<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\DeliveryTicket\Actions\AddDocumentToDeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Tests\TestCase;

class AddDocumentToDeliveryTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_adds_documents_to_delivery_ticket_when_user_is_authorized(): void
    {
        $user = User::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create();

        $inputs = [
            'medias' => [],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $deliveryTicket)
            ->once();

        $action = new AddDocumentToDeliveryTicket;
        $action->handle($user, $deliveryTicket, $inputs);

        // If no exception is thrown, the test passes
        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_exception_when_user_is_not_authorized(): void
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create();

        $inputs = [
            'medias' => [],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $deliveryTicket)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = new AddDocumentToDeliveryTicket;
        $action->handle($user, $deliveryTicket, $inputs);
    }
}
