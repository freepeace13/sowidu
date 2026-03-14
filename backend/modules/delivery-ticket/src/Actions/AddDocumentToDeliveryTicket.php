<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\DeliveryTicket\Contracts\Actions\AddDocumentToDeliveryTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketDocument;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class AddDocumentToDeliveryTicket implements AddDocumentToDeliveryTicketContract
{
    public function handle($user, DeliveryTicket $deliveryTicket, array $inputs)
    {
        // Check user if authorized
        Gate::forUser($user)->authorize('update', $deliveryTicket);

        $validated = $this->validate($inputs);

        // Save documents
        $documents = collect(data_get($validated, 'medias'))
            ->map(function ($mediaUuid) use ($user, $deliveryTicket) {
                $media = Media::findByUuid($mediaUuid);

                $document = $deliveryTicket->documents()
                    ->where('media_file_id', $media->id)
                    ->first();

                if (!$document) {
                    $document = (new DeliveryTicketDocument);
                }

                $document->author()
                    ->associate($user);
                $document->media()
                    ->associate($media);

                return $document;
            });

        $deliveryTicket->documents()->saveMany($documents);
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'medias' => [
                'nullable',
                'array',
            ],
            'medias.*' => [
                'required',
                'exists:media_files,uuid',
            ],
        ])->validate();
    }
}
