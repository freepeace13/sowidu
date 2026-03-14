<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @include('invoice::pdf.styles')
</head>

<body>
    <p class="page-number-container"><span class="page-label"></span></p>

    @include('invoice::pdf.components.footer', ['invoice' => $invoice])

    <div class="invoice-body" id="preview-sheet-container">
        <div class="preview-sheet">
            <div class="invoice-header">
                <div class="header-container">
                    <div class="tw-text-lg tw-w-full payer tw-h-full">
                        <div class="tw-text-xxs address">
                            <div>
                                {{ $invoice->company->name }}
                                {{ $invoice->company->legal_form->legal_form ?? '' }};
                                {{ $invoice->company->address->short_full_address ?? '' }}
                            </div>
                        </div>
                        <div class="tw-text-base">
                            {{ $client->name }}
                            @if (is_string($client?->legal_form))
                                {{ $client->legal_form }}
                            @else
                                {{ $client?->legal_form?->legal_form ?? $client?->legal_form ?? ''}}
                            @endif
                        </div>

                        <!-- Care of -->
                        @if ($invoice->care_of_address && $invoice->care_of_name)
                                                <div class="tw-text-base payer-info">
                                                    {!! collect([
                                $invoice->care_of_name,
                                $invoice->care_of_legalform,
                                $invoice->care_of_address,
                            ])->filter()
                                ->join(', ') !!}
                                                </div>
                        @else
                            <div class="tw-text-base payer-info ">
                                {{ join(', ', array_filter([$client->address->street, $client->address->house_number])) }}
                                <br />
                                {{ $client->address->zipcode ?? '' }} {{ $client->address->city ?? '' }}
                            </div>
                        @endif


                        <div class="tw-flex tw-flex-grow tw-items-end tw-font-semibold tw-text-base invoice-kind ">
                            {{ $invoice->kind->label ?? '' }}

                        </div>
                    </div>
                    <div class="tw-text-left pt-0 contractor tw-w-[18rem]">
                        <div class="tw-flex tw-justify-end contractor-logo-container">
                            <img src="{{ $invoice->company->photo }}" alt="{{ $invoice->company->name }}"
                                class="contractor-logo">
                        </div>
                        <div class="tw-font-semibold tw-text-base">
                            {{ $invoice->company->name }}
                            {{ $invoice->company->legal_form->legal_form ?? '' }}
                        </div>
                        <div class="tw-font-semibold tw-text-sm ">
                            <div>
                                {{ join(', ', array_filter([$invoice->company->address->street, $invoice->company->address->house_number])) }}
                            </div>
                            <div>
                                {{ $invoice->company->address->zipcode ?? '' }}
                                {{ $invoice->company->address->city ?? '' }}
                            </div>
                        </div>
                        <div class="tw-font-semibold tw-text-[10px] tw-underline contractor-contact-info ">
                            <div>
                                <span>
                                    {{ $companyInvoiceDefaults->website ?? '' }}
                                </span>
                            </div>
                            <div>
                                <span>
                                    {{ $companyInvoiceDefaults->company_email ?? ''  }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="invoice-items-container">

                @include('invoice::pdf.components.details', ['invoice' => $invoice])

                @if (filled($invoice->subject) || filled($invoice->description))
                    <div class="tw-w-full subject-and-description">
                        <div class="subject-and-description-container">
                            <div class="subject">
                                {{ $invoice->subject  }}
                            </div>
                            <div class="description">
                                {{ $invoice->description  }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="invoice-items">
                    <div class="invoice-items-table">

                        <table class="w-full line-items-table">
                            <thead>
                                <tr class="tw-h-12">
                                    <th width="6%" class="item-headers tw-text-left">
                                        {{ __('invoices.preview.table.pos') }}
                                    </th>
                                    <th width="7%" class="item-headers tw-text-left">
                                        {{ __('invoices.preview.table.item-count') }}
                                    </th>
                                    <th width="12%" class="item-headers tw-text-left">
                                        {{ __('invoices.preview.table.item-unit') }}
                                    </th>
                                    <th class="item-headers tw-text-left">
                                        {{ __('invoices.preview.table.item-description') }}
                                    </th>
                                    <th width="15%" class="item-headers tw-text-center">
                                        {{ __('invoices.preview.table.price-per-piece') }}
                                    </th>
                                    <th width="15%" class="item-headers tw-text-right">
                                        {{ __('invoices.preview.table.total-price') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoiceItems as $item)
                                    <tr class="item-row no-border tw-align-top">
                                        <td> {{ $item->line_item_number }} </td>
                                        <td> {{ $item->quantity }} </td>
                                        <td class="tw-uppercase">
                                            {{ $item->unit_name }}
                                        </td>
                                        <td class="tw-text-sm">
                                            <span>
                                                {{ $item->name }}
                                            </span>
                                        </td>
                                        <td class="tw-text-right">
                                            <span class="price-per-unit">
                                                {{ $item->price_formatted }}
                                            </span>
                                        </td>
                                        <td class="tw-text-right">
                                            {{ $item->subtotal_formatted }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Invoice Totals --}}
                                @include('invoice::pdf.components.invoice-totals', ['totals' => $invoiceTotals])
                                {{-- End Invoice Totals --}}

                            </tbody>
                        </table>
                    </div>
                    {{-- End Invoice Items Table --}}

                </div>
                {{-- End invoice-items --}}

                <div class="notes-section">
                    <!-- This is just a draft to see if the current page can fit the notes -->
                    <div class="invoice-notes hidden-notes">
                        @include('invoice::pdf.components.invoice-notes')
                    </div>

                    <div class="footer-notes invoice-notes">
                        @include('invoice::pdf.components.invoice-notes')
                    </div>
                </div>

                {{-- End Invoice Items --}}
            </div>
            {{-- End Invoice Items Container --}}
        </div>
        {{-- End Preview Sheet --}}

    </div>
    {{-- End Invoice body --}}

    {{-- Second page header --}}
    @include('invoice::pdf.components.header', ['invoice' => $invoice])

</body>

</html>
