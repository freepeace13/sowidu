<?php

namespace App\Modules\Invoice\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Modules\Invoice\Actions\Preview\GetInvoiceLineItems;
use App\Modules\Invoice\InvoiceService;
use App\Services\CompanyService;
use App\Transformers\Order\OrderTransformer;
use horstoeko\zugferd\codelists\ZugferdCountryCodes;
use horstoeko\zugferd\codelists\ZugferdInvoiceType;
use horstoeko\zugferd\codelists\ZugferdUnitCodes;
use horstoeko\zugferd\codelists\ZugferdVatCategoryCodes;
use horstoeko\zugferd\codelists\ZugferdVatTypeCodes;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferdlaravel\Facades\ZugferdLaravel;
use ReflectionClass;

/**
 * @see https://github.com/horstoeko/zugferd/wiki/Example-ZugferdDocumentBuilder-in-EN16391-profile
 */
class PdfaInvoiceBuilder
{
    protected string $destinationPath;

    protected ZugferdDocumentBuilder $document;

    protected InvoiceService $invoiceService;

    protected Order $order;

    public function __construct(protected Invoice $invoice, protected string $pdfPath)
    {
        $this->document = ZugferdLaravel::createDocumentInEN16931Profile();
        $this->invoiceService = new InvoiceService($invoice);
        $this->order = $invoice->order()
            ->first();
    }

    public function setDestinationPath(string $path): self
    {
        $this->destinationPath = $path;

        return $this;
    }

    public static function make(Invoice $invoice, string $pdfPath): static
    {
        return new static($invoice, $pdfPath);
    }

    protected function buildSellerInformation()
    {
        $company = $this->invoice->company()
            ->first();

        $companyService = new CompanyService($company);

        $legalForm = $companyService->getLegalForm();

        $this->document->setDocumentSeller(
            $this->invoice->biller->name . ' ' . $legalForm,
        );

        if (filled($vatId = $company->vat_identification_number)) {
            $this->document->addDocumentSellerVATRegistrationNumber($vatId);
        }

        if (filled($company->tax_number)) {
            $taxNumber = $vatId ?? $company->tax_number;

            $this->document->addDocumentSellerTaxNumber($taxNumber);

        }

        $address = $company->currentPlace()
            ->first();

        $this->document->setDocumentSellerAddress(
            "{$address->street} {$address->house_number}",
            '',
            '',
            $address->zipcode,
            $address->city,
            $address->country,
        );

        $managingDirector = $this->invoiceService->getManagingDirector();

        $this->document->setDocumentSellerContact(
            data_get($managingDirector, 'name'),
            '',
            '',
            '',
            data_get($managingDirector, 'email'),
        );
    }

    protected function buildCustomerDetails(): void
    {
        $order = $this->order;

        $customer = collect(data_get(
            OrderTransformer::make($order)->withClientFullDetails($order->client->loadMissing(['currentPlace']))
                ->resolve(),
            'client',
        ));

        $legalForm = data_get($customer, 'legal_form.legal_form') ?? data_get($customer, 'legalform') ?? data_get($customer, 'legal_form.abbreviation');

        $this->document->setDocumentBuyer(
            $customer->get('name') . ' ' . $legalForm,
        );

        $address = collect($customer['address']);

        $this->document->setDocumentBuyerAddress(
            $address->get('street') . ' ' . $address->get('house_number'),
            '',
            '',
            $address->get('zipcode'),
            $address->get('city'),
            $address->get('country')['code'] ?? ZugferdCountryCodes::GERMANY,
        );

        $this->document->setDocumentBuyerContact(
            $customer->get('name'),
            $legalForm,
            $customer->get('phone') ?? $customer->get('mobile') ?? '',
            '',
            '',
        );
    }

    protected function buildAdditionalDocuments()
    {
        $order = $this->order;

        $this->document->addDocumentInvoiceSupportingDocumentWithUri(
            $this->invoice->internal_id,
            route('invoices.preview', [
                'invoice' => $this->invoice->uuid,
            ]),
        );

        $this->document->addDocumentTenderOrLotReferenceDocument(
            $order->order_number,
        );

        $this->document->setDocumentProcuringProject(
            $order->order_number,
            $order->description,
        );

        $this->document->addDocumentPaymentTerm(
            $this->invoiceService->getPaymentTermsInstructions(),
        );
    }

    public function merge()
    {
        $order = $this->order;

        $this->document
            ->setDocumentInformation(
                $this->getDocumentNo(), // Document #
                ZugferdInvoiceType::INVOICE,
                $this->invoice->send_date?->toDate() ?? today()->toDate(),
                $this->invoice->currency(),
            );

        if (filled($notes = $this->invoice->notes)) {
            $this->document->addDocumentNote(
                str_replace(["\r\n", "\n", "\r"], PHP_EOL, $notes),
            );
        }

        $this->buildBillingPeriod();

        $this->buildSellerInformation();

        $this->buildCustomerDetails();

        $this->document->setDocumentBuyerOrderReferencedDocument($order->order_number);
        $this->document->setDocumentSellerOrderReferencedDocument(
            $order->order_number,
        );

        $this->document->setDocumentSupplyChainEvent(
            $this->invoice->delivery_date?->toDate(),
        );

        // Invoice line items
        $this->buildInvoiceLineItems();

        $this->buildAdditionalDocuments();

        // Invoice Summary
        $this->buildInvoiceSummary();

        // Merge the PDF's
        ZugferdLaravel::buildMergedPdf(
            $this->document,
            $this->pdfPath,
            $this->destinationPath,
        );

        return $this->destinationPath;
    }

    protected function buildInvoiceSummary()
    {
        $invoiceSummary = $this->invoiceService->summaryService();
        $grandTotal = $invoiceSummary->grandTotal();
        $subtotal = $invoiceSummary->subtotal();
        $netTotalAmount = $invoiceSummary->netAmount();
        $discountTotalAmount = $invoiceSummary->totalDeductions();
        $totalTaxes = $invoiceSummary->totalTaxes();

        $this->document->setDocumentSummation(
            $grandTotal, // Grand Total
            $grandTotal, // Amount to pay
            $subtotal, // Net Total Amount
            0.0, // Charge total amount
            $discountTotalAmount, // Discount Total amount
            $netTotalAmount, // VAT basis total amount
            $totalTaxes, // VAT Total amount
        );
    }

    protected function buildInvoiceLineItems()
    {
        $lineItems = GetInvoiceLineItems::run($this->invoice, ['to_pdfa' => true]);
        $totalVatRate = $this->invoiceService->getTaxes()
            ->pluck('rate')
            ->sum();

        foreach ($lineItems as $index => $lineItem) {
            $lineItem = collect($lineItem);
            $itemType = $lineItem->get('unit_name');

            $this->document->addNewPosition(
                $lineItem->get('line_item_number'),
            );

            $this->document->setDocumentPositionProductDetails(
                $lineItem->get('name'), // Product name
                $lineItem->get('description'), // Product description
                data_get($lineItem, 'details.internal_id'), // sellerAssignedID
                null, // buyerAssignedID
                $itemType, // globalIDType
                data_get($lineItem, 'details.manufacture_id'), // globalID
            );

            // Price
            $this->document->setDocumentPositionNetPrice($lineItem->get('price'));

            // Quantity & Unit Code
            $this->document->setDocumentPositionQuantity(
                (float) $lineItem->get('quantity'),
                $this->getUnitCode($lineItem),
            );

            $this->document->addDocumentPositionTax(
                ZugferdVatCategoryCodes::STAN_RATE,
                ZugferdVatTypeCodes::VALUE_ADDED_TAX,
                $totalVatRate,
            );

            $this->document->setDocumentPositionLineSummation($lineItem->get('subtotal'));
        }
    }

    protected function getUnitCode($lineItem)
    {
        // TODO - Cache this constants values and search from it!
        $zugferdUnitCodes = (new ReflectionClass(ZugferdUnitCodes::class))
            ->getConstants();
        $unitName = data_get($lineItem->get('details'), 'unit_name');

        $zugferdUnitCode = collect($zugferdUnitCodes)
            ->filter(function ($value, $key) use ($unitName) {
                return $value === $unitName || str($key)->afterLast('_') === strtoupper($unitName);
            })
            ->first();

        if (!$zugferdUnitCode) {
            $zugferdUnitCode = ZugferdUnitCodes::REC20_PIECE;
        }

        if ($lineItem->get('is_work_log', false)) {
            $zugferdUnitCode = ZugferdUnitCodes::REC20_HOUR;
        }

        return $zugferdUnitCode;
    }

    protected function buildBillingPeriod(): void
    {
        // Add billing period
        $executionPeriod = $this->invoiceService->getExecutionPeriod();
        $startedAt = data_get($executionPeriod, 'started_at');
        $endedAt = data_get($executionPeriod, 'ended_at');

        $startedAt = $startedAt instanceof \Carbon\Carbon ? $startedAt : \Carbon\Carbon::parse($startedAt);
        $endedAt = $endedAt instanceof \Carbon\Carbon ? $endedAt : \Carbon\Carbon::parse($endedAt);

        $this->document->setDocumentBillingPeriod(
            $startedAt?->toDate(),
            $endedAt?->toDate(),
            $startedAt?->format('Y-m-d') . ' - ' . $endedAt?->format('Y-m-d'),
        );
    }

    protected function getDocumentNo(): string
    {
        return $this->invoice->external_id ?? $this->invoice->internal_id;
    }
}
