<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp

    <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/views/invoice-pdf.css']['file']) }}">


    <style>
        /* @font-face {
            font-family: 'DejaVu Sans';
            font-size: 0.5em;
            src: url('{{ asset('fonts/DejaVuSans.ttf') }}') format("truetype");
        } */

        /* Main styles */
        body {
            /* font-family: 'DejaVu Sans'; */
            margin: 0;
            padding: 0;
            font-size: 12pt;
        }

        * {
            box-sizing: border-box;
        }

        /* DomPDF Header/Footer Definition */
        @page {
            margin: 130px 25px 150px 25px;
            /* top right bottom left */
        }

        header {
            position: fixed;
            top: -110px;
            left: 0;
            right: 0;
            height: 100px;
        }

        footer {
            position: fixed;
            bottom: -130px;
            left: 0;
            right: 0;
            height: 120px;
            border-top: 1px solid #f0f0f0;
            padding-top: 10px;
            font-size: 10pt;
        }

        main {
            margin-top: 0;
            margin-bottom: 0;
        }

        /* Page breaks */
        .page-break {
            page-break-after: always;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }

        /* Keep certain elements together */
        .no-break {
            page-break-inside: avoid;
        }

        /* First page only content */
        .first-page-only {
            display: block;
        }

        /* Last page only content */
        .last-page-only {
            display: block;
        }

        /* Invoice items styles */
        .invoice-item {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <!-- Define header that will appear on every page -->
    <header>
        <div class="tw-flex tw-items-start tw-mb-5 invoice-sheet-header">
            <div class="tw-text-lg tw-w-full payer tw-h-full tw-flex tw-flex-col">
                <div class="tw-text-xxs tw-flex address">
                    <div>Company Address</div>
                </div>
                <div class="tw-text-base"> {{ $invoice['payer']['name'] ?? 'Client Name' }} </div>
                <div class="tw-text-base payer-info">Client Address</div>
                <div class="tw-flex tw-flex-grow tw-items-end tw-font-semibold tw-text-base invoice-kind">
                    Final Invoice
                </div>
            </div>
            <div class="tw-text-left pt-0 contractor tw-w-[18rem]">
                <div class="tw-flex tw-justify-end contractor-logo-container">
                    <img src="http://app.sowidu.test/storage/avatars/0f8e26639ed8b0f1dbf90a14b1acb73d/reach_stacker_1000_QL80_.jpg"
                        class="tw-object-fill w-full contractor-logo" width=150>
                </div>
                <div class="tw-font-semibold tw-text-base">
                    {{ $invoice['contractor']['name'] ?? 'Company Name' }}
                </div>
                <div class="tw-font-semibold tw-text-sm">
                    <div> {{ $invoice['contractor']['address_line_1'] ?? 'Company Address Line 1' }} </div>
                    <div> {{ $invoice['contractor']['address_line_2'] ?? 'Company Address Line 2' }} </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Define footer that will appear on every page -->
    <footer>
        <div class="layout !tw-flex-none no-break row wrap align-end">
            <div class="flex tw-flex tw-text-xs bank-name xs12">
                <div>Bank Details:</div>
                <div class="ml-1"> {{ $invoice['contractor']['bank_name'] ?? '' }} </div>
            </div>
            <div class="flex tw-flex tw-text-xs iban-bic tw-flex-col tw-gap-y-2 xs4">
                <div class="iban tw-flex">
                    <div>IBAN:</div>
                    <div class="ml-1"> {{ $invoice['contractor']['iban'] ?? '' }} </div>
                </div>
                <div class="bic tw-flex">
                    <div>BIC:</div>
                    <div class="ml-1"> {{ $invoice['contractor']['bic'] ?? '' }} </div>
                </div>
            </div>
            <div class="flex tw-flex tw-text-xs vat-hra tw-flex-col tw-gap-y-2 xs4">
                <div class="vat tw-flex">
                    <div>VAT #:</div>
                    <div class="ml-1"> {{ $invoice['contractor']['vat_number'] ?? '' }} </div>
                </div>
                <div class="hra tw-flex">
                    <div>HRA Nr:</div>
                    <div class="ml-1"> {{ $invoice['contractor']['hra_number'] ?? '' }} </div>
                </div>
            </div>
            <div class="flex tw-text-xs !tw-text-right managing-director xs4">
                <div>Managing director:</div>
                <div class="tw-mt-2"> {{ $invoice['contractor']['managing_director'] ?? '' }} </div>
            </div>
        </div>
    </footer>

    <!-- Main content -->
    <main>
        <!-- First page with invoice details -->
        <div class="no-break first-page-only">
            {{-- Invoice Preview Information - Only on first page --}}
            <div class="invoice-preview-information">
                <div class="tw-pb-3 tw-flex tw-justify-between main">
                    <div class="tw-pr-4 invoice-id">
                        <div class="tw-text-xs tw-font-semibold">Invoice:</div>
                        <div>{{ $invoice['number'] ?? 'INV-000' }}</div>
                    </div>
                    <div class="tw-pr-2 order-number">
                        <div class="tw-text-xs tw-font-semibold">Order No.:</div>
                        <div>{{ $invoice['order_number'] ?? 'N/A' }}</div>
                    </div>
                    <div class="tw-ml-auto invoice-date">
                        <div class="tw-font-semibold tw-text-xs">Date:</div>
                        <div class="tw-font-normal">
                            @if(isset($invoice['send_date']))
                                {{ \Carbon\Carbon::parse($invoice['send_date'])->format('d.m.Y') }}
                            @elseif(isset($invoice['created_at']))
                                {{ \Carbon\Carbon::parse($invoice['created_at'])->format('d.m.Y') }}
                            @else
                                {{ now()->format('d.m.Y') }}
                            @endif
                        </div>
                    </div>
                </div>

                @if(isset($invoice['construction_site']) && $invoice['construction_site'])
                    <div class="layout tw-text-xs construction-site row wrap">
                        <div class="flex xs2 pt-0">Construction Site:</div>
                        <div class="flex tw-text-xs xs6 pt-0">{{ $invoice['construction_site'] }}</div>
                    </div>
                @endif

                @if(isset($invoice['execution_start_date']) && isset($invoice['execution_end_date']))
                    <div class="layout tw-text-xs tw-leading-3 execution-period row wrap">
                        <div class="flex xs2 pt-0">Execution Period:</div>
                        <div class="flex xs6 pt-0 tw-flex tw-gap-x-2">
                            <div>{{ \Carbon\Carbon::parse($invoice['execution_start_date'])->format('d.m.Y') }}</div>
                            <div>-</div>
                            <div>{{ \Carbon\Carbon::parse($invoice['execution_end_date'])->format('d.m.Y') }}</div>
                        </div>
                    </div>
                @endif

                @if(isset($invoice['service_recipient']) && $invoice['service_recipient'])
                    <div class="layout tw-text-xs service-recipient row wrap">
                        <div class="flex xs2 py-0">Service Recipient:</div>
                        <div class="flex tw-text-xs xs6 py-0">{{ $invoice['service_recipient'] }}</div>
                    </div>
                @endif
            </div>

            {{-- Invoice Subject - Only on first page --}}
            <div class="tw-w-full subject-and-description">
                <div class="subject-and-description-container">
                    <div class="mt-2 subject">{{ $invoice['subject'] ?? '' }}</div>
                    <div class="tw-text-[12px] tw-whitespace-normal tw-min-h-5 description">
                        @if(isset($invoice['description']))
                            @if(is_array($invoice['description']))
                                {!! nl2br(e(implode("\n", $invoice['description']))) !!}
                            @else
                                {!! nl2br(e($invoice['description'])) !!}
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice Items Table --}}
        <div class="invoice-body mt-2">
            <table class="w-full line-items-table">
                <thead>
                    <tr class="tw-h-12">
                        <th width="6%" class="tw-text-[12px] !tw-font-bold !tw-text-black tw-text-left">Pos</th>
                        <th width="7%" class="tw-text-[12px] !tw-font-bold !tw-text-black tw-text-left">Count</th>
                        <th width="12%" class="tw-text-[12px] !tw-font-bold !tw-text-black tw-text-left">Unit</th>
                        <th class="tw-text-[12px] !tw-font-bold !tw-text-black tw-text-left">Item description</th>
                        <th width="15%" class="tw-text-[12px] !tw-font-bold !tw-text-black tw-text-center">Unit Price
                        </th>
                        <th width="15%" class="tw-text-[12px] !tw-font-bold !tw-text-black tw-text-right">Total Price
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoiceItems as $index => $item)
                        <tr class="invoice-item no-border tw-align-top">
                            <td>{{ $item->line_item_number }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="tw-uppercase">{{ $item->unit_name ?? '--' }}</td>
                            <td class="tw-text-sm tw-text-[13px]">{{ $item->name }}</td>
                            <td class="tw-text-right tw-text-[13px]">
                                <span class="tw-mr-6">{{ $item->price_formatted }}</span>
                            </td>
                            <td class="tw-text-right tw-text-[13px]">{{ $item->subtotal_formatted }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Notes on the last page --}}
        @if(!isset($invoice['show_summary_page']) || !$invoice['show_summary_page'])
            @if(isset($invoice['notes']))
                <div class="layout invoice-notes row wrap mt-4 mb-2 no-break">
                    <div class="flex xs12">
                        <div class="tw-text-xs tw-font-semibold mb-1">Notes:</div>
                        <div class="tw-text-xs">
                            @if(is_array($invoice['notes']))
                                {!! nl2br(e(implode("\n", $invoice['notes']))) !!}
                            @else
                                {!! nl2br(e($invoice['notes'])) !!}
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Summary Page (if enabled) --}}
        @if(isset($invoice['show_summary_page']) && $invoice['show_summary_page'])
            <div class="page-break"></div>
            <div class="no-break">
                <h2 class="tw-text-xl tw-font-bold tw-text-center tw-mb-4">Invoice Summary</h2>

                {{-- Summary table --}}
                <div class="tw-bg-gray-50 tw-rounded-lg tw-p-4 tw-mx-auto tw-w-2/3">
                    <table class="w-full">
                        <tbody>
                            <tr class="tw-border-b tw-border-gray-300">
                                <td class="tw-py-2 tw-font-semibold">Subtotal:</td>
                                <td class="tw-py-2 tw-text-right">
                                    {{ $invoice['currency'] ?? '€' }}{{ number_format($invoice['subtotal'] ?? 0, 2, '.', ',') }}
                                </td>
                            </tr>

                            @if(isset($invoice['discount_amount']) && $invoice['discount_amount'] > 0)
                                <tr class="tw-border-b tw-border-gray-300">
                                    <td class="tw-py-2">Discount
                                        @if(isset($invoice['discount_percentage']) && $invoice['discount_percentage'] > 0)
                                            ({{ $invoice['discount_percentage'] }}%)
                                        @endif
                                    </td>
                                    <td class="tw-py-2 tw-text-right">
                                        -{{ $invoice['currency'] ?? '€' }}{{ number_format($invoice['discount_amount'], 2, '.', ',') }}
                                    </td>
                                </tr>
                            @endif

                            @if(isset($invoice['vat_amount']) && $invoice['vat_amount'] > 0)
                                <tr class="tw-border-b tw-border-gray-300">
                                    <td class="tw-py-2">VAT
                                        @if(isset($invoice['vat_percentage']))
                                            ({{ $invoice['vat_percentage'] }}%)
                                        @endif
                                    </td>
                                    <td class="tw-py-2 tw-text-right">
                                        {{ $invoice['currency'] ?? '€' }}{{ number_format($invoice['vat_amount'], 2, '.', ',') }}
                                    </td>
                                </tr>
                            @endif

                            <tr class="tw-font-bold">
                                <td class="tw-py-2">Total:</td>
                                <td class="tw-py-2 tw-text-right">
                                    {{ $invoice['currency'] ?? '€' }}{{ number_format($invoice['total'] ?? 0, 2, '.', ',') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Payment terms --}}
                @if(isset($invoice['payment_terms']))
                    <div class="tw-mt-6 tw-mx-auto tw-w-2/3">
                        <h3 class="tw-font-semibold tw-mb-2">Payment Terms</h3>
                        <p class="tw-text-sm">{{ $invoice['payment_terms'] }}</p>
                    </div>
                @endif

                {{-- Notes (always shown on summary page if it exists) --}}
                @if(isset($invoice['notes']))
                    <div class="layout invoice-notes row wrap mt-4 mb-2">
                        <div class="flex xs12">
                            <div class="tw-text-xs tw-font-semibold mb-1">Notes:</div>
                            <div class="tw-text-xs">
                                @if(is_array($invoice['notes']))
                                    {!! nl2br(e(implode("\n", $invoice['notes']))) !!}
                                @else
                                    {!! nl2br(e($invoice['notes'])) !!}
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </main>
</body>

</html>