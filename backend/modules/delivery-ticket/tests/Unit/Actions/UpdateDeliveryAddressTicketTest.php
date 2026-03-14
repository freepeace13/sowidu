<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Tests\Unit\Actions;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\DeliveryTicket\Actions\UpdateDeliveryAddressTicket;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Tests\TestCase;

class UpdateDeliveryAddressTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_delivery_address(): void
    {
        $deliveryTicket = DeliveryTicket::factory()->create();
        $newPlace = Place::factory()->create();

        $inputs = [
            'delivery_address' => $newPlace->id,
        ];

        $action = new UpdateDeliveryAddressTicket;
        $action->handle($deliveryTicket, $inputs);

        $deliveryTicket->refresh();

        $this->assertEquals($newPlace->id, $deliveryTicket->delivery_address_id);
    }

    /** @test */
    public function it_fails_validation_when_delivery_address_is_missing(): void
    {
        $this->expectException(ValidationException::class);

        $deliveryTicket = DeliveryTicket::factory()->create();

        $inputs = [];

        $action = new UpdateDeliveryAddressTicket;
        $action->handle($deliveryTicket, $inputs);
    }
}
