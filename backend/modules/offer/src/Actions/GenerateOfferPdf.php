<?php

namespace Modules\Offer\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Facades\Pdf;
use Modules\Offer\Models\Offer;
use Modules\Offer\Models\OfferItem;
use Modules\Offer\OfferService;
use Modules\Offer\Support\Pdf\PathGenerator;
use Modules\Offer\Transformers\OfferItemTransformer;
use Modules\Offer\Transformers\OfferTransformer;

class GenerateOfferPdf
{
    use AsAction;

    public function __construct(
        protected PathGenerator $pathGenerator,
    ) {}

    /**
     * Generate PDF and return the file path.
     */
    public function handle(Offer $offer): string
    {
        $offer->loadMissing([
            'company',
            'order:id,offer_id,order_number',
            'constructionSite',
        ]);

        $service = OfferService::make($offer);

        $offerData = OfferTransformer::make($offer)
            ->withAmounts()
            ->withStatusMetadata()
            ->withFormattedDates()
            ->withConstructionSite($offer->constructionSite)
            ->withOrderInfo($offer?->order)
            ->withCompanyFullDetails($offer->company)
            ->withRecipientDetails($offer->recipient)
            ->withPdfMessage()
            ->toObject();

        $companyLogoPath = $this->toFilePath($offerData->company->photo ?? '');

        $items = $this->items($service, $offer);
        $totals = $this->totals($service, $offer);

        $pdf = Pdf::loadView(
            'offer::components.pdf.offer',
            [
                'offer' => $offerData,
                'companyLogoPath' => $companyLogoPath,
                'companyInvoiceDefaults' => $offerData->company->invoice_defaults,
                'items' => collect($items)
                    ->map(fn ($item) => (object) $item)
                    ->values(),
                'recipient' => $offerData->recipient,
                'totals' => $totals,
            ],
        );

        // Save PDF to file and return path
        $pdfPath = $this->pathGenerator->getPath($offer);

        File::ensureDirectoryExists(dirname($pdfPath));
        $pdf->save($pdfPath);

        return $pdfPath;
    }

    /**
     * Convert storage URL to file path for mPDF.
     */
    protected function toFilePath(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // Extract path after 'storage' (same approach as Invoicify)
        $avatarPath = storage_path('app/public' . Str::after($url, 'storage'));

        return File::exists($avatarPath) ? $avatarPath : '';
    }

    protected function totals(OfferService $service, Offer $offer): array
    {
        $totals = collect([]);
        $currency = $service->currency();

        // Subtotal
        $subtotal = $service->calculation()
            ->subtotal();
        $totals->push([
            $this->buildInvoiceSummaryItem(
                trans('invoices.labels.subtotal'),
                $subtotal,
                [
                    'colspan' => 4,
                ],
                $currency,
            ),
        ]);

        $lastSection = collect([]);

        // Net Amount
        $netAmount = $service->calculation()
            ->netTotal();
        $lastSection->push(
            $this->buildInvoiceSummaryItem(
                trans('invoices.labels.net-amount'),
                $netAmount,
                [
                    'colspan' => 3,
                    'label_colspan' => 2,
                    'is_bold' => true,
                ],
                $currency,
            ),
        );

        // Taxes
        $taxes = collect($offer->properties()
            ->taxes()
            ->all())
            ->map(
                fn ($tax) => $this->buildInvoiceSummaryItem(
                    data_get($tax, 'name') . ' ' . data_get($tax, 'rate') . '%',
                    $service->calculation()
                        ->applyTaxRate(
                            $subtotal,
                            data_get($tax, 'rate'),
                        ),
                    [
                        'colspan' => 3,
                        'label_colspan' => 2,
                        'is_bold' => true,

                    ],
                ),
            );
        $lastSection = $lastSection->concat($taxes->toArray());

        // Grand total
        $grandTotal = $service->calculation()
            ->grandTotal();
        $lastSection->push(
            $this->buildInvoiceSummaryItem(
                trans('invoices.labels.grand-total'),
                $grandTotal,
                [
                    'colspan' => 3,
                    'label_colspan' => 2,
                    'border_bottom_double' => true,
                    'is_bold' => true,
                ],
                $currency,
            ),
        );

        return $totals->merge([$lastSection])->toArray();
    }

    protected function buildInvoiceSummaryItem(
        string $label,
        float $amount,
        array $settings = [],
        string $currency = 'EUR',
    ) {
        return array_merge([
            'label' => $label,
            'amount' => $amount,
            'amount_formatted' => number_to_money($amount, $currency),
        ], $settings);
    }

    protected function items(OfferService $service, Offer $offer): array
    {
        return collect(
            $service->groupItems($offer
                ->items()
                ->get()
                ->map(
                    fn (OfferItem $item) => (new OfferItemTransformer($item))
                        ->withDetails()
                        ->withCurrency($service->currency())
                        ->resolve(),
                )),
        )->map(function ($item, $index) {
            $unitName = '';

            $unitName = data_get($item, 'details.unit_name');

            if (blank($unitName)) {
                $unitName = '--';
            }

            $lineItem = [
                'id' => $item['id'],
                'line_item_number' => str($index + 1)->padLeft(3, '0')
                    ->toString(),
                'quantity' => $item['quantity_formatted'],
                'unit_name' => $unitName,
                'name' => $item['name'],
                'price' => $item['price'],
                'price_formatted' => $item['price_formatted'],
                'subtotal' => $item['subtotal'],
                'subtotal_formatted' => $item['subtotal_formatted'],
            ];

            return $lineItem;
        })
            ->toArray();
    }
}
