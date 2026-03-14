<?php

namespace App\Transformers;

use App\Enums\InvoiceStatus;
use App\Models\Company;
use App\Models\Order;
use App\Modules\Invoice\Services\InvoiceCalculationService;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Sowidu\SharedData\SharedData;

/**
 * @property \App\Models\Invoice $resource
 */
class InvoiceTransformer extends Transformer
{
    public $subtotal = 0;

    public $toDeductTotal = 0;

    public function toArray($request)
    {
        $type = $this->resource->type;
        $kind = $this->resource->kind;

        return [
            'id' => $this->resource->id,
            'uuid' => $this->resource->uuid,
            'internal_id' => $this->resource->internal_id,
            'external_id' => $this->resource->external_id,
            'delivery_date' => $this->resource->delivery_date,
            'payment_date' => $this->resource->payment_date,
            'send_date' => $this->resource->send_date,
            'deliverer_id' => $this->resource->deliverer_id,
            'type' => [
                'name' => $type ? snake_to_readable($type->name) : null,
                'value' => $type?->value,
            ],
            'notes' => $this->resource->notes,
            'is_paid' => $this->resource->is_paid,
            'subject' => $this->resource->subject,
            'description' => $this->resource->description,
            'kind' => [
                'value' => $kind?->value,
                'label' => $kind?->trans(),
            ],
        ];
    }

    public function withConstructionSite(?\App\Models\Place $constructionSite = null): self
    {
        return $this->state(fn () => [
            'construction_site' => (new PlaceTransformer($constructionSite))
                ->withId()
                ->withGoogleMapUrl()
                ->withShortFullAddress()
                ->resolve(),
        ]);
    }

    public function withStatus()
    {
        $status = $this->resource->status ?? InvoiceStatus::DRAFT;

        $labelKey = strtolower($status->name);

        return $this->state(
            fn () => [
                'status' => [
                    'label' => trans("invoices.labels.{$labelKey}"),
                    'value' => $status->value,
                    'is_draft' => $status == InvoiceStatus::DRAFT,
                    'is_pending' => $status == InvoiceStatus::SENT,
                    'is_sent' => $status == InvoiceStatus::SENT,
                    'is_paid' => $status == InvoiceStatus::PAID,
                    'color' => $status->color(),
                    'icon' => $status->icon(),
                ],
                'can_be_edited' => $status == InvoiceStatus::DRAFT,
                'can_be_deleted' => $status == InvoiceStatus::DRAFT,
                'can_add_payments' => $status == InvoiceStatus::SENT,
            ],
        );
    }

    public function withOrderClientDetails(Order $order)
    {
        $client = data_get(
            (new OrderTransformer($order))
                ->withClientFullDetails(
                    $order->client->loadMissing(['currentPlace']),
                )
                ->resolve(),
            'client',
        );

        return $this->state(
            fn () => [
                'client' => $client,
            ],
        );
    }

    public function withBillerDetails(): self
    {
        $billerDetails = [
            'biller_id' => $this->resource->biller_id,
            'biller_type' => $this->resource->biller_type,
            'type' => $this->resource->biller_type,
        ];

        $biller = $this->resource->biller;
        if (same_class(Company::class, $biller)) {
            $settings = $biller->settings()
                ->invoiceDefaults();

            data_fill($billerDetails, 'column_values', [
                'name' => $biller->name,
                'photo' => get_company_avatar_url($biller),
                'email' => $settings->get('company_email'),
                'phone' => $settings->get('company_phone'),
            ]);
        }

        // Check if 'currency' key exists before merging
        if (!$this->resource->biller_details->has('currency')) {
            $this->resource->biller_details->put(
                'currency',
                app(SharedData::class)->get('defaults.currency'),
            );
        }

        return $this->state(fn () => [
            'biller' => $this->resource->biller_details->merge($billerDetails),
        ]);
    }

    public function withOrderDetails(): self
    {
        return $this->state(fn () => [
            'order' => [
                'id' => $this->resource->order->id,
                'order_number' => $this->resource->order->order_number,
            ],
        ]);
    }

    public function withOrderFullDetails(Order $order)
    {
        return $this->state(fn () => [
            'order' => (new OrderTransformer($order))
                ->withClientPrimaryDetails($order->client)
                ->withDeliveryAddress()
                ->resolve(),
        ]);
    }

    public function withDeliveryAddress(): self
    {
        return $this->state(fn () => [
            'delivery_address' => (new PlaceTransformer($this->resource->deliveryAddress))
                ->withId()
                ->resolve(),
        ]);
    }

    public function withDocuments(): self
    {
        return $this->state(
            fn () => [
                'documents' => $this->resource->documents
                    ->map(
                        fn ($document) => array_merge(
                            (new MediaTransformer($document->media))
                                ->withCreatedAt()
                                ->resolve(),
                            [
                                'document_id' => $document->id,
                            ],
                        ),
                    ),
            ],
        );
    }

    public function withItems(): self
    {
        return $this->state(
            fn () => [
                'items' => $this->resource->items->map(
                    fn ($item) => (new InvoiceItemTransformer($item))->withDetails()
                        ->resolve(),
                ),
            ],
        );
    }

    public function withTotalPrice(?float $totalAmount, string $currency)
    {
        return $this->state(
            fn () => [
                'total_amount' => $totalAmount,
                'total_amount_formatted' => format_currency($totalAmount, $currency),
            ],
        );
    }

    public function withTaxes(Collection|array $taxes)
    {
        if (is_array($taxes)) {
            $taxes = collect($taxes);
        }

        $invoiceService = InvoiceCalculationService::run($this->resource);

        return $this->state(
            fn () => [
                'taxes' => $taxes->map(
                    function ($tax) use ($invoiceService) {
                        return (new TaxTransformer($tax))
                            ->withAmount($invoiceService->taxAmount($tax))
                            ->resolve();
                    },
                ),
            ],
        );
    }

    public function withCompanyDetails(Company $company)
    {
        return $this->state(
            fn () => [
                'company' => (new CompanyTransformer($company))
                    ->withTaxSettings()
                    ->resolve(),
            ],
        );
    }

    public function withCompanyFullDetails(Company $company)
    {
        return $this->state(
            fn () => [
                'company' => (new CompanyTransformer($company))
                    ->withTaxSettings()
                    ->withInvoiceDefaults()
                    ->withCurrentAddress($company->currentPlace()
                        ->first())
                    ->withType()
                    ->resolve(),
            ],
        );
    }

    public function withTotalWage(float $sum)
    {
        return $this->state(
            fn () => [
                'total_wage' => $sum,
                'total_wage_formatted' => number_to_money(
                    $sum,
                    $this->resource->currency(),
                ),
            ],
        );
    }

    public function withExecutionPeriod($startedAt, $endedAt)
    {
        $startedAt = Carbon::parse($startedAt)->format('Y-m-d');
        $endedAt = Carbon::parse($endedAt)->format('Y-m-d');

        return $this->state(
            fn () => [
                'execution_period' => [
                    'start' => $startedAt,
                    'end' => $endedAt,
                ],
            ],
        );
    }

    public function withPreviewLayout(): self
    {
        return $this->state(fn () => [
            'preview_layout' => $this->resource->preview_layout,
        ]);
    }

    public function withFormattedDates(): self
    {
        return $this->state(fn () => [
            'payment_date' => $this->resource?->payment_date?->format('d.m.Y'),
            'send_date' => $this->resource?->send_date?->format('d.m.Y'),
            'updated_at' => $this->resource?->updated_at?->format('d.m.Y'),
        ]);
    }

    public function withCareOf(): InvoiceTransformer
    {
        $this->load('careOf');

        $address = $this->resource->careOf ? "<br />{$this->resource->careOf->currentPlace->street}
            {$this->resource->careOf->currentPlace->house_number} <br />
            {$this->resource->careOf->currentPlace->zipcode}
            {$this->resource->careOf->currentPlace->city}" : null;

        return $this->state(fn () => [
            'care_of_name' => $this->resource->careOf ? 'C/O ' . $this->resource->careOf->name : null,
            'care_of_legalform' => $this->resource->careOf->legalform ?? null,
            'care_of_address' => $address,
        ]);
    }
}
