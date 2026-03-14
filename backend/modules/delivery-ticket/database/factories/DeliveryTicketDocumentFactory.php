<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketDocument;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class DeliveryTicketDocumentFactory extends Factory
{
    protected $model = DeliveryTicketDocument::class;

    public function definition(): array
    {
        return [
            'delivery_ticket_id' => DeliveryTicket::factory(),
            'user_id' => User::factory(),
            'media_file_id' => fn () => Media::create([
                'uuid' => Str::uuid()->toString(),
                'name' => $this->faker->word(),
                'file_name' => $this->faker->word() . '.pdf',
                'mime_type' => 'application/pdf',
                'disk' => 'local',
                'size' => $this->faker->numberBetween(1000, 100000),
                'category' => 'documents',
                'model_type' => DeliveryTicketDocument::class,
                'model_id' => 0,
            ])->id,
        ];
    }
}
