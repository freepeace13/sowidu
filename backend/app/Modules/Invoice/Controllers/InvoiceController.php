<?php

namespace App\Modules\Invoice\Controllers;

use App\Enums\InvoiceKind;
use App\Enums\InvoiceType;
use App\Http\Controllers\Controller;
use App\Models\CatalogItemUnit;
use App\Models\Invoice;
use App\Modules\Invoice\Actions\CreateInvoice;
use App\Modules\Invoice\Actions\DeleteInvoice;
use App\Modules\Invoice\Actions\UpdateInvoice;
use App\Modules\Invoice\InvoiceService;
use App\Modules\Invoice\Services\InvoiceCalculationService;
use App\Modules\Invoice\Services\InvoicePaymentService;
use App\Modules\Invoice\Services\InvoiceSummaryService;
use App\Repositories\InvoiceRepository;
use App\Services\CompanyService;
use App\Services\MediaFileService;
use App\Support\Vuetify\CreateOptions;
use App\Transformers\CompanyTransformer;
use App\Transformers\Invoice\DeductionTransformer;
use App\Transformers\Invoice\InvoiceDeductionTransformer;
use App\Transformers\Invoice\InvoiceSummaryTransformer;
use App\Transformers\InvoiceItemTransformer;
use App\Transformers\InvoicePaymentTransformer;
use App\Transformers\InvoiceTransformer;
use App\Transformers\MediaTransformer;
use App\Transformers\PaginatorTransformer;
use App\Transformers\TaxTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Modules\Catalog\Services\CatalogService;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $company = $request->company();
        $query = [
            'q',
            'dateAdded',
            'paymentDate',
            'type',
            'invoiceStatus',
        ];
        $filters = $request->only([
            'q',
            'dateAdded',
            'paymentDate',
            'type',
            'invoiceStatus',
        ]);

        $collection = collect([]);

        if ($request->hasHeader('x-inertia-partial-data')) {
            $invoiceRepository = InvoiceRepository::make($user, $company)
                ->filters($filters)
                ->whereHas('order')
                ->with([
                    'client',
                    'biller',
                    'taxes',
                    'deliveryAddress',
                    'order',
                    'order.currentPlace',
                ])
                ->latest();

            $collection = $invoiceRepository->paginate($request->get('count', 15));
        }

        return Inertia::render(
            'Index',
            [
                'permissions' => [
                    'can_manage_invoices' => $request->user()
                        ->can(
                            'create',
                            Invoice::class,
                        ),

                    'can_add_payments' => $user->can(
                        'addPayments',
                        Invoice::class,
                    ),
                ],
                'invoiceDefaults' => data_get(
                    CompanyTransformer::make($request->company())
                        ->withInvoiceDefaults()
                        ->resolve(),
                    'invoice_defaults',
                ),
                'filters' => blank($filters) ? [
                    'q' => null,
                ] : $filters,
                'invoiceTypes' => fn () => collect(
                    CreateOptions::from(InvoiceType::options())->build(),
                )->values()
                    ->toArray(),
                'allowedTypes' => fn () => Arr::flatten(
                    MediaFileService::allowedMimetypes(),
                ),
                'invoices' => Inertia::lazy(
                    fn () => collect($collection->items())
                        ->map(function (Invoice $invoice) {
                            $invoiceCalculation = InvoiceCalculationService::run($invoice);
                            $currency = $invoiceCalculation->currency();
                            $grandTotal = $invoiceCalculation->grandTotal();

                            return InvoiceTransformer::make($invoice)->withBillerDetails()
                                ->withOrderDetails()
                                ->withTotalPrice($grandTotal, $currency)
                                ->withStatus()
                                ->withOrderClientDetails($invoice->order)
                                ->withDeliveryAddress()
                                ->resolve();
                        }),
                ),
                'paginator' => Inertia::lazy(
                    fn () => (new PaginatorTransformer($collection))->resolve(),
                ),

                'invoiceKinds' => fn () => CreateOptions::from(InvoiceKind::cases())->build(),
                // 'summaryTotal' => InvoiceRepository::make($user, $company)
                //     ->filters($filters)
                //     ->calculateSummaryTotal(),
            ],
        );
    }

    public function show(Request $request, Invoice $invoice)
    {
        $user = $request->user();

        $invoiceService = InvoiceService::run($invoice);
        $company = $invoice->company;

        $executionPeriod = $invoiceService->getExecutionPeriod();

        return Inertia::render(
            'Show',
            [
                'invoice' => fn () => (new InvoiceTransformer($invoice))
                    ->withStatus()
                    ->withOrderDetails()
                    ->withOrderClientDetails($invoice->order)
                    ->withCompanyDetails($company)
                    ->withConstructionSite($invoiceService->getConstructionSite())
                    ->withExecutionPeriod(
                        data_get($executionPeriod, 'started_at'),
                        data_get($executionPeriod, 'ended_at'),
                    )
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

                'permissions' => [
                    'can_update_items' => $user->can('manageItems', $invoice),
                    'can_update_documents' => $user->can('manageDocuments', $invoice),
                    'can_manage_taxes' => $user->can('manageTaxes', $invoice),
                    'can_view_payments' => $user->can('viewPayments', $invoice),
                    'can_manage_payments' => $user->can('managePayments', $invoice),
                    'invoice_is_editable' => $invoice->isEditable(),
                    'can_delete_invoice' => $user->can('delete', $invoice),
                    'can_send_invoice' => $user->can('sendToClient', $invoice),
                    'can_mark_as_paid' => $user->can('markAsPaid', $invoice),
                ],

                'items' => fn () => $invoice
                    ->items()
                    ->with([
                        'item',
                        'company',
                    ])
                    ->get()
                    ->map(
                        fn ($item) => (new InvoiceItemTransformer($item))
                            ->withItemType()
                            ->withItemDetails()
                            ->withActions($user)
                            ->withFormattedValues($invoiceService->currency())
                            ->resolve(),
                    ),

                'documents' => Inertia::lazy(
                    fn () => $invoice
                        ->documents()
                        ->with('media')
                        ->get()
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
                ),

                'allowedTypes' => fn () => Arr::flatten(
                    MediaFileService::allowedMimetypes(),
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
                        $invoiceCalculation = InvoicePaymentService::run($invoice);
                        $currency = $invoiceCalculation->currency();
                        $outstandingBalance = $invoiceCalculation->outstandingBalance();
                        $paid = $invoiceCalculation->totalAmountsPaid();
                        $invoiceStatus = $invoice->status;

                        return [
                            'status_color' => $invoiceStatus->color(),
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

    public function store(Request $request)
    {
        $invoice = CreateInvoice::run(
            $request->user(),
            $request->company(),
            $request->all(),
        );

        flash_success(trans('invoices.message.success.creating'));

        $referer = $request->header('referer');
        if (get_route_name($referer) === 'orders.show.invoices.index') {
            return to_route('orders.show.invoices.show', [
                'order' => $invoice->order_id,
                'invoice' => $invoice->id,
            ]);
        }

        flash_data(['invoice' => $invoice->id]);

        return back();
    }

    public function update(Request $request, Invoice $invoice)
    {
        UpdateInvoice::run(
            $request->user(),
            $request->company(),
            $invoice,
            $request->all(),
        );

        flash_success(trans('invoices.message.success.updating'));

        return back();
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        DeleteInvoice::run(
            $request->user(),
            $request->company(),
            $invoice,
        );

        flash_success(trans('invoices.message.success.deleting'));

        return to_route('invoices.index');
    }
}
