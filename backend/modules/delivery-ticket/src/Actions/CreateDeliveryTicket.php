<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use App\Enums\DeliveryTicketType;
use App\Models\Addressbook;
use App\Models\Order;
use App\Rules\OwnedByCompany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\DeliveryTicket\Contracts\Actions\CreateDeliveryTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketDocument;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class CreateDeliveryTicket implements CreateDeliveryTicketContract
{
    public function handle($user, $company, array $inputs)
    {
        // Check user if authorized
        Gate::forUser($user)->authorize('create', DeliveryTicket::class);

        // Validate inputs
        $validated = $this->validate($inputs);

        // Save
        $deliveryTicket = DeliveryTicket::make(
            Arr::only($validated, ['delivery_date', 'external_id', 'type']),
        );

        $deliveryTicket->company()->associate($company);
        $deliveryTicket->user()->associate($user);

        $deliveryTicket->deliveryAddress()->associate(data_get($validated, 'delivery_address.id'));
        $deliveryTicket->order()->associate(data_get($validated, 'order.id'));
        if (data_get($validated, 'deliverer.id')) {
            $deliveryTicket->deliverer()->associate(data_get($validated, 'deliverer.id'));
        }
        $deliveryTicket->save();

        // Save documents
        $documents = collect(data_get($validated, 'medias'))
            ->map(function ($mediaUuid) use ($user) {
                $document = (new DeliveryTicketDocument);
                $document->author()
                    ->associate($user);
                $document->media()
                    ->associate(Media::findByUuid($mediaUuid));

                return $document;
            });

        $deliveryTicket->documents()->saveMany($documents);

        return $deliveryTicket;
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'deliverer' => 'array',
            'deliverer.id' => [
                'integer',
                new OwnedByCompany(Addressbook::class, 'team_id'),
            ],
            'order' => 'required|array',
            'order.id' => [
                'required',
                'integer',
                new OwnedByCompany(Order::class, 'team_id'),
            ],
            'delivery_address' => 'required|array',
            'delivery_address.id' => [
                'required',
                'integer',
                'exists:places,id',
            ],
            'delivery_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
            'medias' => [
                'nullable',
                'array',
            ],
            'medias.*' => [
                'required',
                'exists:media_files,uuid',
            ],
            'external_id' => [
                'required',
            ],
            'type' => [
                'required',
                Rule::in(DeliveryTicketType::values()),
            ],
        ])->validate();
    }
}
