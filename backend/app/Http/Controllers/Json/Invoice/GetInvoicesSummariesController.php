<?php

namespace App\Http\Controllers\Json\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Modules\Invoice\Services\InvoiceCalculationService;
use App\Repositories\InvoiceRepository;
use Illuminate\Http\Request;

class GetInvoicesSummariesController extends Controller
{
    public function __invoke(Request $request)
    {
        $summaries = collect([]);

        InvoiceRepository::make(
            $request->user(),
            $request->company(),
        )->filters(
            $request->only([
                'q',
                'dateAdded',
                'invoiceStatus',
                'type',
                'paymentDate',
                'plannedStartDate',
                'plannedFinishDate',
            ]),
        )
            ->select([
                'id',
                'subtotal',
                'net_amount',
                'total_vat',
                'grand_total',
            ])
            ->chunk(10, function ($invoices) use ($summaries) {
                $data = $invoices->map(function (Invoice $invoice) {
                    if (
                        $invoice?->net_amount
                        && $invoice?->total_vat
                        && $invoice?->grand_total
                    ) {
                        return [
                            'total_without_vat' => $invoice->net_amount,
                            'total_vat' => $invoice->total_vat,
                            'total_with_vat' => $invoice->grand_total,
                        ];
                    }

                    $invoiceCalculation = InvoiceCalculationService::run($invoice);

                    $netAmount = $invoiceCalculation->netAmount();
                    $totalTaxes = $invoiceCalculation->totalTaxes();
                    $grandTotal = $invoiceCalculation->grandTotal();

                    return [
                        'total_without_vat' => $netAmount,
                        'total_vat' => $totalTaxes,
                        'total_with_vat' => $grandTotal,
                    ];
                });

                $summaries->push([
                    'total_without_vat' => $data->sum('total_without_vat'),
                    'total_vat' => $data->sum('total_vat'),
                    'total_with_vat' => $data->sum('total_with_vat'),
                ]);
            });

        $currency = get_company_currency($request->company());
        $totalWithoutVat = $summaries->sum('total_without_vat');
        $totalVat = $summaries->sum('total_vat');
        $totalWithVat = $summaries->sum('total_with_vat');

        return response()->json([
            'total_without_vat' => $totalWithoutVat,
            'total_vat' => $totalVat,
            'total_with_vat' => $totalWithVat,
            'total_without_vat_formatted' => format_currency($totalWithoutVat, $currency),
            'total_vat_formatted' => format_currency($totalVat, $currency),
            'total_with_vat_formatted' => format_currency($totalWithVat, $currency),
        ]);
    }
}
