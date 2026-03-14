<?php

namespace Modules\Offer\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Contracts\External\AddressbookServiceContract;
use Modules\Offer\Contracts\External\OrderServiceContract;
use Modules\Offer\Enums\OfferStatus;
use Modules\Offer\Enums\OfferType;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;

class CreateOffer
{
    use AsAction;

    public function __construct(
        protected AddressbookServiceContract $addressbookService,
        protected OrderServiceContract $orderService,
    ) {}

    public function handle(Model $user, Model $company, array $inputs): Offer
    {
        Gate::forUser($user)->authorize('create', Offer::class);

        $validated = $this->validate($inputs);

        // Create the offer
        $offer = Offer::make(
            data_only($validated, [
                'type',
                'title',
                'description',
                'offer_date',
            ]),
        );

        $offer->recipientable()
            ->associate(
                $this->addressbookService->findOrFail(data_get($validated, 'recipient.id')),
            );
        $offer->author()
            ->associate($user);
        $offer->company()
            ->associate($company);

        if ($orderId = data_get($validated, 'order.id')) {
            $offer->order()
                ->associate($this->orderService->findOrFail($orderId));
        }

        $offer->status = OfferStatus::DRAFT();

        $offer->save();

        $offerService = OfferService::make($offer);

        // Attach tax
        $offerService->attachDefaultTaxes();

        // Save offer configuration default values
        $offerService->saveOfferConfigurationDefaults();

        return $offer;
    }

    public function validate(array $inputs): array
    {
        $addressbookClass = config('offer.models.addressbook');
        $orderClass = config('offer.models.order');

        return Validator::make($inputs, [
            'recipient.id' => [
                'required',
                'integer',
                'exists:addressbooks,id',
            ],
            'recipient.type' => [
                'required',
                'string',
                'in:addressbooks',
            ],

            'type' => [
                'required',
                'string',
                'in:' . implode(',', OfferType::values()),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'offer_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'order.id' => [
                'nullable',
                'integer',
                'exists:orders,id',
            ],
        ])->validate();
    }
}
