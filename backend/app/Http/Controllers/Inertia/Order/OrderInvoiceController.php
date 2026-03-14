<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Enums\InvoiceKind;
use App\Enums\InvoiceType;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\CatalogItemUnit;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Modules\Invoice\InvoiceService;
use App\Modules\Invoice\Services\InvoiceCalculationService;
use App\Modules\Invoice\Services\InvoicePaymentService;
use App\Modules\Invoice\Services\InvoiceSummaryService;
use App\Repositories\OrderRepository;
use App\Services\CompanyService;
use App\Support\Vuetify\CreateOptions;
use App\Transformers\CompanyTransformer;
use App\Transformers\Invoice\DeductionTransformer;
use App\Transformers\Invoice\InvoiceDeductionTransformer;
use App\Transformers\Invoice\InvoiceSummaryTransformer;
use App\Transformers\InvoiceItemTransformer;
use App\Transformers\InvoicePaymentTransformer;
use App\Transformers\InvoiceTransformer;
use App\Transformers\MediaTransformer;
use App\Transformers\Order\OrderTransformer;
use App\Transformers\TaxTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Catalog\Services\CatalogService;

class OrderInvoiceController extends InertiaController
{
    public function index(Request $request, Order $order)
    {
        $user = $request->user();
        $company = $request->company();

        abort_unless(
            $user->can('accessInvoices', $order),
            403,
            trans('validation.403'),
        );

        return Inertia::render('Order/Invoices/OrderInvoices', [
            'invoices' => OrderRepository::make($request->user(), $company)
                ->setOrder($order)
                ->invoices()
                ->map(
                    function (Invoice $invoice) {
                        $invoiceCalculation = InvoiceCalculationService::run($invoice);

                        return InvoiceTransformer::make($invoice)
                            ->withTotalPrice(
                                $invoiceCalculation->grandTotal(),
                                $invoiceCalculation->currency(),
                            )
                            ->withStatus()
                            ->resolve();
                    },
                ),

            'order' => OrderTransformer::make($order)
                ->withClientPrimaryDetails($order->client)
                ->withDeliveryAddress()
                ->withContractorPrimaryDetails($order->contractor)
                ->resolve(),

            'permissions' => [
                'can_manage_order_invoices' => $user->can('manageOrderInvoices', $order),
            ],

            'invoiceTypes' => fn () => collect(
                CreateOptions::from(InvoiceType::options())->build(),
            )->values()
                ->toArray(),

            'authCompany' => fn () => [
                'invoice_defaults' => $this->getCompanyInvoiceDefaults(
                    $company,
                ),
            ],

            'invoiceKinds' => fn () => CreateOptions::from(InvoiceKind::cases())->build(),
        ]);
    }

    protected function getCompanyInvoiceDefaults(?Company $company = null): ?array
    {
        return $company ? data_get(
            CompanyTransformer::make($company)->withInvoiceDefaults()
                ->resolve(),
            'invoice_defaults',
        ) : null;
    }

    public function show(Request $request, Order $order, Invoice $invoice)
    {
        // Check if the user has permission to view the invoice when status is draft
        if ($invoice->isDraft() && auth_is_private_user()) {
            flash_error(trans('validation.403'));

            return redirect()->route('orders.show.invoices.index', $order);
        }

        $user = $request->user();

        abort_unless(
            $user->can('accessInvoices', $order),
            403,
            trans('validation.403'),
        );

        $invoiceItems = $invoice
            ->items()
            ->with(['item'])
            ->get();

        $invoiceService = InvoiceService::run($invoice);

        return Inertia::render(
            'Order/Invoices/OrderInvoice',
            [
                'invoice' => InvoiceTransformer::make($invoice)
                    ->withStatus()
                    ->withOrderDetails()
                    ->withOrderClientDetails($invoice->order)
                    ->withCompanyDetails($invoice->company)
                    ->withCareOf()

                    ->resolve(),

                'invoiceSummary' => Inertia::lazy(function () use ($invoice) {
                    $invoiceSummary = InvoiceSummaryService::run($invoice);
                    $invoiceSummary->clearCache();
                    $currency = $invoiceSummary->currency();
                    $subtotal = $invoiceSummary->subtotal();
                    $netAmount = $invoiceSummary->netAmount();
                    $grandTotal = $invoiceSummary->grandTotal();

                    $invoiceDeductions = $invoiceSummary->invoiceDeductions()
                        ->map(
                            function ($deduction) {
                                $invoiceDeduction = $deduction->deductible;

                                return array_merge(InvoiceDeductionTransformer::make($invoiceDeduction)
                                    ->withAmount(
                                        $invoiceDeduction->net_amount ?? InvoiceSummaryService::run($invoiceDeduction)->netAmount(),
                                    )
                                    ->resolve(), [
                                        'id' => $deduction->id,
                                    ]);
                            },
                        )
                        ->toArray();

                    $manualDeductions = $invoiceSummary->manualDeductions()
                        ->map(
                            function ($manualDeduction) use ($invoiceSummary, $currency) {
                                $deductible = $manualDeduction->deductible;

                                return DeductionTransformer::make($manualDeduction)
                                    ->withDeductible(
                                        $deductible,
                                        $invoiceSummary->getManualDeductionAmount($deductible),
                                        $currency,
                                    )
                                    ->resolve();
                            },
                        )
                        ->toArray();

                    $invoiceSummaryPayload = InvoiceSummaryTransformer::make((object) [
                        'currency' => $currency,
                        'subtotal' => $subtotal,
                        'grand_total' => $grandTotal,
                        'net_amount' => $netAmount,
                    ])
                        ->withTaxes(
                            $invoiceSummary->taxes()
                                ->get()
                                ->map(
                                    fn ($tax) => (new TaxTransformer($tax))
                                        ->withAmount($invoiceSummary->taxAmount($tax))
                                        ->resolve(),
                                )
                                ->toArray(),
                        )
                        ->withInvoiceDeductions($invoiceDeductions)
                        ->withManualDeductions($manualDeductions);

                    if (filled($invoiceDeductions) && filled($manualDeductions)) {
                        $invoiceSummaryPayload->withSubtotalAfterDeduction($invoiceSummary->subtotalAfterDeduction())
                            ->withVatOnSubtotalAfterDeduction($invoiceSummary->calculateVatOnSubtotalAfterDeductions())
                            ->withSubtotalAfterDeductionWithVat($invoiceSummary->subtotalAfterDeductionWithVat());
                    }

                    return $invoiceSummaryPayload->resolve();
                }),

                'order' => OrderTransformer::make($order)->resolve(),

                'items' => fn () => $invoiceItems
                    ->map(
                        fn ($item) => (new InvoiceItemTransformer($item))
                            ->withItemType()
                            ->withItemDetails()
                            ->withActions($user)
                            ->withFormattedValues($invoiceService->currency())
                            ->resolve(),
                    ),

                'documents' => fn () => $invoice
                    ->loadMissing(['documents.media'])
                    ->documents
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

                'permissions' => [
                    'can_update_items' => $user->can('manageItems', $invoice),
                    'can_update_documents' => $user->can('manageDocuments', $invoice),
                    'can_manage_taxes' => $user->can('manageTaxes', $invoice),
                    'can_view_payments' => $user->can('viewPayments', $invoice),
                    'can_manage_payments' => $user->can('managePayments', $invoice),
                    'invoice_is_editable' => $invoice->isEditable(),
                ],

                'warnEmployeeRate' => $invoiceService->checkEmployeeRateNotSet($invoiceItems),

                'addableTaxes' => Inertia::lazy(
                    fn () => $invoiceService->getAddableTaxes()
                        ->map(fn ($tax) => TaxTransformer::make($tax)->resolve()),
                ),

                'payments' => Inertia::lazy(
                    fn () => $invoiceService->getPayments()
                        ->map(
                            fn ($payment) => InvoicePaymentTransformer::make($payment)
                                ->withAmountFormatted(
                                    CompanyService::make($invoice->company)
                                        ->currencyName(),
                                )
                                ->withMethodMeta()
                                ->resolve(),
                        ),
                ),
                'itemTypeOptions' => fn () => CreateOptions::from(
                    CatalogService::make(
                        $request->user(),
                        $request->company(),
                    )
                        ->allItemTypes(),

                )->build(),

                'addableTaxes' => Inertia::lazy(
                    fn () => $invoiceService->getAddableTaxes()
                        ->map(fn ($tax) => TaxTransformer::make($tax)->resolve()),
                ),

                'payments' => Inertia::lazy(
                    fn () => $invoiceService->getPayments()
                        ->map(
                            fn ($payment) => InvoicePaymentTransformer::make($payment)
                                ->withAmountFormatted(
                                    CompanyService::make($invoice->company)
                                        ->currencyName(),
                                )
                                ->withMethodMeta()
                                ->resolve(),
                        ),
                ),
                'paymentsSummary' => Inertia::lazy(
                    function () use ($invoice) {
                        $invoicePayment = InvoicePaymentService::run($invoice);
                        $currency = $invoicePayment->currency();
                        $outstandingBalance = $invoicePayment->outstandingBalance();
                        $paid = $invoicePayment->totalAmountsPaid();

                        return [
                            'paid' => $paid,
                            'paid_formatted' => number_to_money($paid, $currency),
                            'outstanding' => $outstandingBalance,
                            'outstanding_formatted' => number_to_money($outstandingBalance, $currency),
                        ];
                    },
                ),
                'unitOptions' => fn () => CreateOptions::from(CatalogItemUnit::all())
                    ->setTextKey('name')
                    ->setValueKey('id')
                    ->build(),
            ],
        );
    }
}
