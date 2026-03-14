<?php

namespace App\Actions\Order\File\OutgoingInvoice;

use App\Models\Company;
use App\Models\Order;
use App\Services\MediaFileService;
use App\Services\Order\OrderService;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class AttachOutgoingInvoiceToOrder
{
    use InteractsWithImpersonator;

    public function attach($user, Order $order, array $params, ?Company $company): Order|\Illuminate\Http\JsonResponse
    {
        $validated = $this->validate($params);

        $media = Arr::get($validated, 'media', null);
        $attachment = MediaFileService::make($this->user())->findByUuidOrFail($media, ['id']);

        $service = OrderService::make($user, $company);

        throw_validation_unless(
            $service->isInvolvedOnOrder($order),
            'You are not involved on this order.',
        );

        $service->attachOutgoingInvoice($order, $attachment);

        return $order;
    }

    protected function validate(array $params)
    {
        return Validator::make($params, [
            'media' => [
                'required',
                'exists:media_files,uuid',
            ],
        ], [
            'media.required' => 'Your file is missing, please upload an PDF or choose from your media files.',
        ])->validated();
    }
}
