<?php

namespace App\Actions\Order\File\Media;

use App\Actions\Media\CreateMediaAddressTag;
use App\Actions\Media\TagMediaWithCategory;
use App\Models\Company;
use App\Models\Order;
use App\Services\MediaFileService;
use App\Services\Order\OrderService;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class AttachMediaToOrder
{
    use InteractsWithImpersonator;

    public function attach($user, Order $order, array $params, ?Company $company): Order|JsonResponse
    {
        $validated = $this->validate($params);

        $service = OrderService::make($user, $company);

        throw_validation_unless(
            $service->isInvolvedOnOrder($order),
            'You are not involved on this order.',
        );

        // Iterate on `medias`
        collect(Arr::get($validated, 'medias', []))
            ->each(function ($mediaUuid) use ($validated, $order, $service, $company, $user) {
                $attachment = MediaFileService::make($this->user())->findByUuidOrFail($mediaUuid, ['id']);

                // Check if media is already attached to the order
                if ($service->mediaIsNotAttached($order, $attachment)) {
                    $service->attachMedia($order, $attachment);
                }

                // Tag delivery_address
                if (Arr::has($validated, 'address')) {
                    $addressTagger = new CreateMediaAddressTag;
                    $addressTagger->tag(
                        $user,
                        $attachment,
                        Arr::only($validated, ['address']),
                        $company->id,
                    );
                }

                // Tag order media with category
                if (Arr::get($validated, 'category')) {
                    $categoryTagger = new TagMediaWithCategory;
                    $categoryTagger->tag(
                        $user,
                        $attachment,
                        Arr::only($validated, ['category']),
                        $company->id,
                    );
                }
            });

        return $order;
    }

    protected function validate(array $params)
    {
        return Validator::make($params, [
            'medias' => [
                'required',
                'array',
            ],
            'medias.*' => [
                'required',
                'exists:media_files,uuid',
            ],
            'address' => ['required', 'array'],
            'category' => [
                'nullable',
                'string',
            ],
        ], [
            'media.required' => 'Your file is missing, please upload an image or choose from your media files.',
        ])->validated();
    }
}
