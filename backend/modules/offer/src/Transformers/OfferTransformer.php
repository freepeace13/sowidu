<?php

namespace Modules\Offer\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Modules\Offer\Contracts\External\AddressbookServiceContract;
use Modules\Offer\Contracts\External\CompanyServiceContract;
use Modules\Offer\Contracts\External\PlaceServiceContract;
use Modules\Offer\Contracts\External\UserServiceContract;

/**
 * @property \Modules\Offer\Models\Offer $resource
 */
class OfferTransformer extends Transformer
{
    public function toArray($request)
    {
        $type = $this->resource->type;

        return [
            'id' => $this->resource->id,
            'uuid' => $this->resource->uuid,
            'internal_id' => $this->resource->internal_id,
            'type' => [
                'name' => snake_to_readable($type->name),
                'value' => $type->value,
            ],
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'status' => $this->resource->status,
            'offer_date' => $this->resource->offer_date,
            'recipientable_id' => $this->resource->recipientable_id,
            'recipientable_type' => $this->resource->recipientable_type,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'execution_period_start' => $this->resource->execution_period_start,
            'execution_period_end' => $this->resource->execution_period_end,
        ];
    }

    public function withProperties(): self
    {
        return $this->state(
            fn () => [
                'properties' => $this->resource->properties,
            ],
        );
    }

    public function withConstructionSite(?Model $constructionSite = null): self
    {
        $placeService = app(PlaceServiceContract::class);
        $place = $placeService->transformForOffer($constructionSite);

        return $this->state(fn () => [
            'construction_site' => $place,
        ]);
    }

    public function withFormattedDates(): self
    {
        return $this->state(
            fn () => [
                'updated_at_formatted' => $this->resource->updated_at?->format('d.m.Y H:i'),
                'offer_date_formatted' => $this->resource->offer_date?->format('d.m.Y'),
                'execution_period_start_formatted' => $this->resource->execution_period_start?->format('d.m.Y'),
                'execution_period_end_formatted' => $this->resource->execution_period_end?->format('d.m.Y'),
            ],
        );
    }

    public function withAuthPermissions(): self
    {
        return $this->state(
            fn () => [
                'permissions' => [
                    'editable' => $this->resource->authCan('edit'),
                    'deletable' => $this->resource->authCan('delete'),
                ],
            ],
        );
    }

    public function withStatusMetadata(): self
    {
        return $this->state(
            fn () => [
                'status_metadata' => [
                    'color' => $this->resource->status->color(),
                    'icon' => $this->resource->status->icon(),
                    'label' => $this->resource->status->trans(),
                    'is_draft' => $this->resource->isDraft(),
                    'is_sent' => $this->resource->isSent(),
                    'is_accepted' => $this->resource->isAccepted(),
                    'is_rejected' => $this->resource->isRejected(),
                    'is_cancelled' => $this->resource->isCancelled(),
                ],
            ],
        );
    }

    public function withOrderInfo(?Model $order): self
    {
        $orderId = $order?->id;

        return $this->state(
            fn () => [
                'order' => [
                    'id' => $orderId,
                    'url' => $orderId
                        ? route('orders.show', ['order' => $orderId])
                        : null,
                    'order_number' => $order?->order_number,
                ],
            ],
        );
    }

    public function withRecipientInfo(Model $recipient): self
    {
        $addressbookService = app(AddressbookServiceContract::class);
        $addressbook = $addressbookService->transformWithAddress($recipient);

        $columnValues = data_get($addressbook, 'column_values', []);

        return $this->state(
            fn () => [
                'recipient' => array_merge(
                    $columnValues,
                    [
                        'addressbook' => $addressbook,
                    ],
                ),
            ],
        );
    }

    public function withAmounts(): self
    {
        return $this->state(
            fn () => [
                'subtotal' => $this->resource->subtotal,
                'subtotal_formatted' => number_to_money(
                    $this->resource->subtotal,
                ),

                'net_amount' => $this->resource->net_amount,
                'net_amount_formatted' => number_to_money(
                    $this->resource->net_amount,
                ),
                'total_vat' => $this->resource->total_vat,
                'total_vat_formatted' => number_to_money(
                    $this->resource->total_vat,
                ),
                'grand_total' => $this->resource->grand_total,
                'grand_total_formatted' => number_to_money(
                    $this->resource->grand_total,
                ),
            ],
        );
    }

    public function withTaxes(array $taxes): self
    {
        return $this->state(fn () => [
            'taxes' => array_map(
                fn ($tax) => [
                    ...$tax,
                    'amount_formatted' => number_to_money($tax['amount']),
                ],
                $taxes,
            ),
        ]);
    }

    public function withCompanyFullDetails(Model $company): self
    {
        $companyService = app(CompanyServiceContract::class);

        return $this->state(
            fn () => [
                'company' => $companyService->transformWithFullDetails($company),
            ],
        );
    }

    protected function buildServiceRecipient(array $columnValues): string
    {
        $name = data_get($columnValues, 'name');
        $legalForm = data_get($columnValues, 'legal_form.legal_form') ?? data_get($columnValues, 'legal_form') ?? '';
        $address = data_get($columnValues, 'address.short_full_address');

        return collect([
            trim("$name $legalForm"),
            $address,
        ])->filter()
            ->join(', ');
    }

    public function withRecipientDetails(Model $recipient): self
    {
        $companyService = app(CompanyServiceContract::class);
        $addressbookService = app(AddressbookServiceContract::class);
        $userService = app(UserServiceContract::class);

        // Determine recipient type by checking model class
        $recipientClass = get_class($recipient);
        $companyClass = config('offer.models.company');
        $addressbookClass = config('offer.models.addressbook');
        $userClass = config('offer.models.user');

        $recipientData = match (true) {
            $recipientClass === $companyClass || is_a($recipient, $companyClass) => array_merge(
                $companyService->transformForRecipient($recipient),
                ['is_company' => true],
            ),
            $recipientClass === $addressbookClass || is_a($recipient, $addressbookClass) => array_merge(
                Arr::get(
                    $addressbookService->transformWithAddress($recipient),
                    'column_values',
                    [],
                ),
                ['is_company' => $addressbookService->isForeignOrganization($recipient)],
            ),
            $recipientClass === $userClass || is_a($recipient, $userClass) => $userService->transformForRecipient($recipient),
            default => [],
        };

        $serviceRecipient = $this->buildServiceRecipient($recipientData);

        return $this->state(
            fn () => [
                'recipient' => array_merge(
                    $recipientData,
                    [
                        'service_recipient' => $serviceRecipient,
                    ],
                ),
            ],
        );
    }

    public function withPdfMessage(): self
    {
        return $this->state(
            fn () => [
                'message' => $this->resource->message,
                'subject' => $this->resource->subject,
                'notes' => $this->resource->notes,
            ],
        );
    }
}
