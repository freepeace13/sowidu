<?php

namespace Modules\Invoicify\Support;

use App\Transformers\Invoice\DeductionManualTransformer;
use App\Transformers\Invoice\InvoiceDeductionTransformer;
use App\Transformers\InvoiceTransformer;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Invoicify\Actions\Preview\GetInvoiceLineItems;
use Modules\Invoicify\Data\CareOf;
use Modules\Invoicify\Data\ClosingBlock;
use Modules\Invoicify\Data\InvoiceDetails;
use Modules\Invoicify\Data\Item;
use Modules\Invoicify\Data\ItemTable;
use Modules\Invoicify\Data\Metadata;
use Modules\Invoicify\Data\PaymentDetails;
use Modules\Invoicify\Data\Recipient;
use Modules\Invoicify\Data\Sender;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Services\InvoiceService;
use Modules\Invoicify\Services\InvoiceSummaryService;
use Modules\Invoicify\Views\InvoicePdfView;

class InvoicePdfFactory
{
    protected static function formatAddress($address)
    {
        return sprintf(
            "%s %s \n %s %s",
            data_get($address, 'street'),
            data_get($address, 'house_number'),
            data_get($address, 'zipcode'),
            data_get($address, 'city'),
        );
    }

    public static function make(Invoice $invoice): InvoicePdfView
    {
        $service = InvoiceService::run($invoice);

        $executionPeriod = $service->getExecutionPeriod();
        $startedAt = data_get($executionPeriod, 'started_at');
        $endedAt = data_get($executionPeriod, 'ended_at');

        $transformedInvoice = (new InvoiceTransformer($invoice))
            ->withStatus()
            ->withOrderFullDetails($invoice->order)
            ->withOrderClientDetails($invoice->order)
            ->withCompanyFullDetails($invoice->company)
            ->withExecutionPeriod($startedAt, $endedAt)
            ->withTotalWage($service->totalWage())
            ->withCareOf()
            ->withPreviewLayout()
            ->resolve();

        $sender = self::sender($transformedInvoice);

        return new InvoicePdfView(
            sender: $sender,
            recipient: self::recipient($transformedInvoice),
            careOf: self::careOf($transformedInvoice),
            invoiceDetails: self::invoiceDetails($transformedInvoice),
            itemTable: self::items($invoice),
            paymentDetails: self::paymentDetails($transformedInvoice),
            closingBlock: self::closingBlock($service, $invoice),
            metadata: new Metadata(
                title: data_get($transformedInvoice, 'internal_id'),
                author: data_get($transformedInvoice, 'company.name'),
                subject: data_get($transformedInvoice, 'subject'),
                keywords: implode(', ', [
                    'invoice',
                    data_get($transformedInvoice, 'kind.label'),
                    data_get($transformedInvoice, 'company.name'),
                    data_get($transformedInvoice, 'subject'),
                ]),
            ),
        );
    }

    protected static function paymentDetails($transformedInvoice)
    {
        return new PaymentDetails(
            bankName: data_get($transformedInvoice, 'company.invoice_defaults.bank_name'),
            iban: data_get($transformedInvoice, 'company.invoice_defaults.iban'),
            bic: data_get($transformedInvoice, 'company.invoice_defaults.bic'),
            hrb: data_get($transformedInvoice, 'company.invoice_defaults.commercial_register_number'),
            vat: data_get($transformedInvoice, 'company.vat_identification_number'),
            managingDirector: data_get($transformedInvoice, 'company.invoice_defaults.managing_director.name'),
        );
    }

    protected static function closingBlock($service, $invoice)
    {
        return new ClosingBlock(
            paymentDate: Carbon::parse($invoice->payment_date)
                ->locale('de_DE')
                ->toDateString(),
            totalWageCosts: number_to_money($service->totalWage()),
            closingRemarks: $invoice->notes,
        );
    }

    protected static function invoiceDetails($transformedInvoice)
    {
        return new InvoiceDetails(
            invoiceNo: data_get($transformedInvoice, 'internal_id'),
            invoiceDate: Carbon::parse(data_get($transformedInvoice, 'send_date'))
                ->locale('de_DE')
                ?->toDateString(),
            orderNo: data_get($transformedInvoice, 'order.order_number'),
            constructionSite: data_get(
                $transformedInvoice,
                'order.delivery_address.short_full_address',
            ),
            executionPeriod: implode(' - ', data_get($transformedInvoice, 'execution_period')),
            serviceRecipient: data_get($transformedInvoice, 'client.service_recipient'),
            legalForm: data_get($transformedInvoice, 'client.legal_form.legal_form'),
        );
    }

    protected static function careOf($transformedInvoice): CareOf
    {
        return new CareOf(
            careOfName: data_get($transformedInvoice, 'care_of_name'),
            careOfLegalForm: data_get($transformedInvoice, 'care_of_legalform'),
            careOfAddress: data_get($transformedInvoice, 'care_of_address'),
        );
    }

    protected static function recipient($transformedInvoice)
    {
        return new Recipient(
            name: data_get($transformedInvoice, 'client.name') ?? '',
            deliveryAddress: self::formatAddress(data_get($transformedInvoice, 'order.delivery_address')) ?? '',
            returnAddress: data_get($transformedInvoice, 'client.address.short_full_address') ?? '',
            additionalNote: data_get($transformedInvoice, 'kind.label') ?? '',
            legalForm: data_get($transformedInvoice, 'client.legalform'),
        );
    }

    protected static function sender($invoice)
    {
        $avatarPath = storage_path('app/public' . Str::after(
            data_get($invoice, 'company.photo'),
            'storage',
        ));

        return new Sender(
            name: data_get($invoice, 'company.name'),
            presentAddress: self::formatAddress(data_get($invoice, 'company.address')),
            logoImgPath: File::exists($avatarPath) ? $avatarPath : config('invoicify.image_placeholder'),
            emailAddress: data_get($invoice, 'company.invoice_defaults.company_email'),
            websiteUrl: data_get($invoice, 'company.invoice_defaults.website'),
            legalForm: data_get($invoice, 'company.legal_form.legal_form'),
        );
    }

    protected static function items($invoice)
    {
        $lineItems = GetInvoiceLineItems::run($invoice);

        $totalsSummary = self::totalsSummary($invoice);

        return new ItemTable(
            title: $invoice->subject,
            caption: $invoice->description,
            totalsSummary: $totalsSummary,
            items: collect($lineItems)
                ->reject(fn ($item) => !Arr::has($item, [
                    'line_item_number',
                    'quantity',
                    'unit_name',
                    'name',
                    'price_formatted',
                    'subtotal_formatted',
                ]))
                ->map(fn ($item) => new Item(
                    sequenceNo: $item['line_item_number'],
                    quantity: $item['quantity'],
                    unit: $item['unit_name'],
                    description: $item['name'],
                    unitPrice: $item['price_formatted'],
                    totalPrice: $item['subtotal_formatted'],
                ))
                ->all(),
        );
    }

    protected static function totalsSummary($invoice): array
    {
        $summary = InvoiceSummaryService::run($invoice);

        $currency = $summary->currency();

        $subtotal = $invoice->subtotal ?? $summary->subtotal();
        $grandTotal = $invoice->grand_total ?? $summary->grandTotal();

        $netAmount = $summary->netAmount();

        $invoiceDeductions = $summary->invoiceDeductions()
            ->map(function ($invoiceDeduction) use ($currency) {
                $items = collect([]);

                $invoice = $invoiceDeduction->deductible;
                $summary = InvoiceSummaryService::run($invoice);

                $currency = $summary->currency();
                $subtotal = $invoice->subtotal ?? $summary->subtotal();
                $grandTotal = $invoice->grand_total ?? $summary->grandTotal();

                $netAmount = $summary->netAmount();

                $data = InvoiceDeductionTransformer::make($invoice)
                    ->withInvoiceSummary(
                        $subtotal,
                        $grandTotal,
                        $currency,
                    )
                    ->withAmount($netAmount)
                    ->withTaxes(
                        $summary->taxes()
                            ->get(),
                    )
                    ->resolve();

                $items->push([
                    'prefix' => data_get($data, 'label'),
                    'label' => trans('invoices.labels.subtotal'),
                    'value' => number_to_money($netAmount),
                ]);

                $taxes = collect(data_get($data, 'taxes'))
                    ->map(fn ($tax) => [
                        'label' => $tax['name'] . ' ' . $tax['rate'] . '%',
                        'value' => number_to_money($tax['amount']),
                    ])
                    ->all();

                $items = $items->merge($taxes);

                $items->push([
                    'label' => trans('invoices.labels.grand-total'),
                    'value' => number_to_money($grandTotal),
                ]);

                return $items->toArray();

            })
            ->toArray();

        $manualDeductions = $summary->manualDeductions()
            ->pluck('deductible')
            ->map(function ($deduction) use ($summary, $currency) {
                $tranformed = DeductionManualTransformer::make($deduction)
                    ->withAmount(
                        $summary->getManualDeductionAmount($deduction),
                        $currency,
                    )
                    ->resolve();

                return [
                    'label' => data_get($tranformed, 'label'),
                    'value' => number_to_money(data_get($tranformed, 'amount')),
                ];
            })
            ->all();

        $taxes = $summary->taxes()
            ->get()
            ->map(fn ($tax) => [
                'label' => $tax->name . ' ' . $tax->rate . '%',
                'value' => number_to_money($summary->taxAmount($tax)),
            ])
            ->all();

        return [
            [
                [
                    'label' => trans('invoices.labels.subtotal'),
                    'value' => number_to_money($subtotal, $currency),
                ],
                ...$manualDeductions,
            ],
            ...$invoiceDeductions,
            [
                [
                    'label' => trans('invoices.labels.net-amount'),
                    'value' => number_to_money($netAmount, $currency),
                ],
                ...$taxes,
                [
                    'label' => trans('invoices.labels.grand-total'),
                    'value' => number_to_money($grandTotal, $currency),
                ],
            ],
        ];
    }
}
