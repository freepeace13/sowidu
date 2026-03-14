<?php

namespace App\Actions\DeliveryTicket;

use App\Actions\Traits\AsAction;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketDocument;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class AddDocumentToDeliveryTicket
{
    use AsAction;

    public function handle(User $user, DeliveryTicket $deliveryTicket, array $inputs)
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
