<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\DeliveryTicket\Actions\RemoveDocumentToDeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketDocument;
use Tests\TestCase;

class RemoveDocumentToDeliveryTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_removes_document_from_delivery_ticket_when_user_is_authorized(): void
    {
        $user = User::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create();
        $document = DeliveryTicketDocument::factory()->create([
            'delivery_ticket_id' => $deliveryTicket->id,
        ]);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $deliveryTicket)
            ->once();

        $action = new RemoveDocumentToDeliveryTicket;
        $action->handle($user, $deliveryTicket, $document);

        $this->assertDatabaseMissing('delivery_ticket_documents', [
            'id' => $document->id,
        ]);
    }

    /** @test */
    public function it_throws_exception_when_user_is_not_authorized(): void
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $deliveryTicket = DeliveryTicket::factory()->create();
        $document = DeliveryTicketDocument::factory()->create([
            'delivery_ticket_id' => $deliveryTicket->id,
        ]);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $deliveryTicket)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = new RemoveDocumentToDeliveryTicket;
        $action->handle($user, $deliveryTicket, $document);
    }
}
