<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\Addressbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\DeliveryTicket\Actions\UpdateDelivererTicket;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Tests\TestCase;

class UpdateDelivererTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_deliverer(): void
    {
        $deliveryTicket = DeliveryTicket::factory()->create();
        $newDeliverer = Addressbook::factory()->create();

        $inputs = [
            'deliverer' => $newDeliverer->id,
        ];

        $action = new UpdateDelivererTicket;
        $action->handle($deliveryTicket, $inputs);

        $deliveryTicket->refresh();

        $this->assertEquals($newDeliverer->id, $deliveryTicket->deliverer_id);
    }

    /** @test */
    public function it_fails_validation_when_deliverer_is_missing(): void
    {
        $this->expectException(ValidationException::class);

        $deliveryTicket = DeliveryTicket::factory()->create();

        $inputs = [];

        $action = new UpdateDelivererTicket;
        $action->handle($deliveryTicket, $inputs);
    }
}
