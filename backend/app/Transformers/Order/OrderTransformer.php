<?php

namespace App\Transformers\Order;

use App\Enums\OrderType;
use App\Models\Addressbook;
use App\Models\Company;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Order\Traits\WithOrderStatusStyles;
use App\Transformers\Addressbook\AddressbookTransformer;
use App\Transformers\CompanyTransformer;
use App\Transformers\PlaceTransformer;
use App\Transformers\Transformer;
use Illuminate\Support\Arr;

/**
 * @property \App\Models\Order $resource
 */
class OrderTransformer extends Transformer
{
    use WithOrderStatusStyles;

    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'order_number' => $this->resource->order_number,
            'type' => $this->resource->type,
            'type_name' => $this->resource->type_name,
            'description' => $this->resource->description,
            'status' => $this->resource->status,
            'status_text' => snake_to_readable($this->resource->status->name),
            'order_date' => $this->resource->order_date,
            'planned_start_date' => $this->resource->planned_start_date,
            'planned_finish_date' => $this->resource->planned_finish_date,
            'client_addressbook_id' => $this->resource->client_addressbook_id,
        ];
    }

    public function withClientPrimaryDetails(Addressbook|User|Company|null $client = null)
    {
        return $this->state(function (array $attributes) use ($client) {
            $this->resource->loadMissing(['clientAddressbook.currentPlace']);
            $place = $this->resource->clientAddressbook?->currentPlace;

            return [
                'client' => [
                    'name' => $this->clientName($client),
                    'email' => $client->email,
                    'photo' => $this->clientPhoto($client),
                    'address' => (new PlaceTransformer($place))
                        ->withId()
                        ->resolve(),
                    'phone' => $client?->phone ?? $client?->mobile,
                ],
            ];
        });
    }

    public function withContractorPrimaryDetails(Addressbook|Company $contractor)
    {
        $morphData = [
            'id' => $contractor->getKey(),
            'type' => $contractor->getMorphClass(),
        ];

        return $this->state(function (array $attributes) use ($contractor, $morphData) {
            if (morph_is($contractor, 'company')) {
                return [
                    'contractor' => array_merge(
                        (new CompanyTransformer($contractor))->withCurrentAddress($contractor->currentPlace)
                            ->resolve(),
                        $morphData,
                    ),
                ];
            }

            return [
                'contractor' => array_merge([
                    'name' => $this->contractorName($contractor),
                    'email' => $contractor->email,
                    'photo' => $contractor->photo ?? get_company_avatar_url($contractor),
                ], $morphData),
            ];
        });
    }

    protected function clientPhoto(Addressbook|User|Company $client): string
    {
        if (morph_is($client, Company::class)) {
            return get_company_avatar_url($client);
        }

        if (morph_is($client, User::class)) {
            return get_user_avatar_url($client);
        }

        return $client->photo ?? get_user_avatar_url($client);
    }

    protected function clientName(Addressbook|User|Company $client): string
    {
        return $client->name ?? $client->full_name;
    }

    protected function contractorName(Addressbook|User|Company $contractor): string
    {
        return $contractor->name ?? $contractor->full_name;
    }

    public function withClientFullDetails(Addressbook|User|Company $client)
    {
        $clientData = match (true) {
            $client instanceof Company => $this->buildCompanyAsClient($client),
            $client instanceof Addressbook => $this->buildAddressbookAsClient($client),
            $client instanceof User => $this->buildUserAsClient($client),
        };

        // Merge service_recipient
        $name = data_get($clientData, 'name');
        $legalForm = data_get($clientData, 'legal_form.legal_form') ?? data_get($clientData, 'legal_form') ?? '';
        $address = data_get($clientData, 'address.short_full_address');

        $serviceRecipient = collect([
            trim("$name $legalForm"),
            $address,
        ])->filter()
            ->join(', ');

        data_fill($clientData, 'legal_form', $legalForm);

        return $this->state(fn () => [
            'client' => array_merge(
                $clientData,
                [
                    'service_recipient' => $serviceRecipient,
                ],
            ),
        ]);
    }

    protected function buildUserAsClient(User $user): array
    {
        return [
            'name' => $this->clientName($user),
            'email' => $user->email,
            'photo' => $this->clientPhoto($user),
            'address' => (new PlaceTransformer($user->currentPlace))
                ->withId()
                ->withShortFullAddress()
                ->resolve(),

            'is_company' => false,
            'phone' => $user->phone ?? $user->mobile,
        ];
    }

    public function buildAddressbookAsClient(Addressbook $client): array
    {
        return array_merge(
            Arr::get(
                (new AddressbookTransformer($client))->withAddress()
                    ->resolve(),
                'column_values',
                [],
            ),
            ['is_company' => $this->identifyClientType($client)],
        );
    }

    protected function identifyClientType($client): bool
    {
        if (morph_is($client, Addressbook::class)) {
            if ($client->isForeign()) {
                return $client->isForeignOrganization();
            }
        }

        return false;
    }

    protected function buildCompanyAsClient(Company $company)
    {
        $company->load('user');

        return array_merge(
            (new CompanyTransformer($company))
                ->withCurrentAddress($company->currentPlace)
                ->withCompanyOwnerDetails($company->user)
                ->withType()
                ->resolve(),
            ['is_company' => true],
        );
    }

    public function withDeliveryAddress()
    {
        return $this->state(function (array $attributes) {
            return [
                'delivery_address' => (new PlaceTransformer($this->resource->deliveryAddress))
                    ->withId()
                    ->withGoogleMapUrl()
                    ->withShortFullAddress()
                    ->resolve(),
            ];
        });
    }

    public function withContractorDetails($contractor = null)
    {
        $contractor = $contractor ?? $this->resource->contractor;
        $contractor->loadMissing(['currentPlace']);

        return $this->state(function (array $attributes) use ($contractor) {
            if (!$contractor) {
                // TODO contractor is not registered - fetch contractor data from addressbooks
            }

            return [
                'contractor' => (new CompanyTransformer($contractor))
                    ->withCurrentAddress($contractor->currentPlace)
                    ->withType()
                    ->resolve(),
            ];
        });
    }

    public function withRequiresResponse(User $user, ?Company $company = null)
    {
        return $this->state(function (array $attributes) use ($user, $company) {
            $service = OrderService::make($user, $company);

            $toStatus = $service->getNeededResponseValue($this->resource)
                ?? $this->resource->status;

            return [
                'response' => [
                    'requires' => $service->isRequiresResponse($this->resource),
                    'action' => [
                        'title' => snake_to_readable($toStatus->name),
                        'value' => $toStatus?->value,
                    ],
                    'dialog' => $service->getResponseDialogData($toStatus),
                ],
            ];
        });
    }

    public function withIsRequireResponse(User $user, ?Company $company = null)
    {
        return $this->state(function (array $attributes) use ($user, $company) {
            return [
                'is_require_response' => OrderService::make($user, $company)
                    ->isRequiresResponse($this->resource),
            ];
        });
    }

    public function withStatus(User $user, ?Company $company = null)
    {
        $service = OrderService::make($user, $company);
        $toStatus = $service->getNeededResponseValue($this->resource)
            ?->name
            ?? $this->resource->status->name;

        return $this->state(function (array $attributes) use ($toStatus) {
            return [
                'status' => [
                    'color' => $this->orderStatusRowColor($toStatus),
                    'value' => $this->resource->status,
                    'text' => snake_to_readable($toStatus),
                    'icon' => $this->orderStatusIcon($toStatus),
                    'icon_color' => $this->orderStatusDialogColor($toStatus),
                    'title' => $this->orderStatusTitle($toStatus),
                    'description' => $this->orderStatusDescription($toStatus),
                ],
            ];
        });
    }

    public function withTotalTimeRendered(?string $time)
    {
        return $this->state(fn () => [
            'total_time_rendered' => $time ?? '0',
        ]);
    }

    public function withOrderType(OrderType $orderType)
    {
        return $this->state(fn () => [
            'order_type' => [
                'description' => $orderType->description(),
                'value' => $orderType,
            ],
        ]);
    }

    public function withInvoiceCount(): OrderTransformer
    {
        return $this->state(fn () => [
            'invoices_count' => $this->resource->invoices()
                ->count(),
        ]);
    }
}
