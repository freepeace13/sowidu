<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Attachment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class AddDocumentToInvoice
{
    use AsAction;

    public function handle(User $user, Invoice $invoice, array $inputs)
    {
        // Check user if authorized
        Gate::forUser($user)->authorize('manageDocuments', $invoice);

        $validated = $this->validate($inputs);

        // Save documents
        $invoice->documents()->saveMany(
            collect(data_get($validated, 'documents'))
                ->map(function ($uuId) use ($invoice, $user) {
                    $media = Media::findByUuid($uuId);
                    $mediaId = $media->getKey();

                    // Check if this media is already attach to this invoice
                    if (
                        $invoice->documents()
                            ->where('media_file_id', $mediaId)
                            ->exists()
                    ) {
                        return;
                    }

                    return new Attachment([
                        'media_file_id' => $mediaId,
                        'user_id' => $user->getKey(),
                    ]);
                })
                ->filter(),
        );
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'documents' => [
                'nullable',
                'array',
            ],
            'documents.*' => [
                'required',
                'exists:media_files,uuid',
            ],
        ])->validate();
    }
}
