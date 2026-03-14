<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @include('offer::pdf.components.styles')
</head>

<body>
    <p class="page-number-container"><span class="page-label"></span></p>

    @include('offer::pdf.components.footer', ['offer' => $offer])


    <div class="invoice-body" id="preview-sheet-container">
        <div class="preview-sheet">
            <div class="invoice-header">
                <div class="header-container">
                    <div class="tw-text-lg tw-w-full payer tw-h-full">
                        <div class="tw-text-xxs address">
                            <div>
                                {{ $offer->company->name }}
                                {{ $offer->company->legal_form->legal_form ?? '' }};
                                {{ $offer->company->address->short_full_address ?? '' }}
                            </div>
                        </div>
                        <div class="tw-text-base">
                            {{ $recipient->name }}
                            {{ $recipient->legal_form ?? $recipient?->legal_form?->legal_form ?? ''}}
                        </div>


                        <div class="tw-text-base payer-info ">
                            {{ join(', ', array_filter([$recipient->address->street, $recipient->address->house_number])) }}
                            <br />
                            {{ $recipient->address->zipcode ?? '' }} {{ $recipient->address->city ?? '' }}
                        </div>
                    </div>
                    <div class="tw-text-left pt-0 contractor tw-w-[18rem]">
                        <div class="tw-flex tw-justify-end contractor-logo-container">
                            @if($companyLogoPath)
                                <img src="{{ $companyLogoPath }}" alt="{{ $offer->company->name }}"
                                    class="contractor-logo">
                            @endif
                        </div>
                        <div class="tw-font-semibold tw-text-base">
                            {{ $offer->company->name }}
                            {{ $offer->company->legal_form->legal_form ?? '' }}
                        </div>
                        <div class="tw-font-semibold tw-text-sm ">
                            <div>
                                {{ join(', ', array_filter([$offer->company->address->street, $offer->company->address->house_number])) }}
                            </div>
                            <div>
                                {{ $offer->company->address->zipcode ?? '' }}
                                {{ $offer->company->address->city ?? '' }}
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
                @include('offer::pdf.components.details', ['offer' => $offer])

                @if (filled($offer->subject) || filled($offer->message))
                    <div class="tw-w-full subject-and-description">
                        <div class="subject-and-description-container">
                            <div class="subject">
                                {{ $offer->subject }} subject
                            </div>
                            <div class="description">
                                {{ $offer->message }}
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
                                @foreach ($items as $item)
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

                                @include('offer::pdf.components.totals', [
                                    'totals' => $totals,
                                ])


                            </tbody>
                        </table>
                    </div>
                    {{-- End Invoice Items Table --}}

                </div>
                {{-- End invoice-items --}}
                <div class="notes-section">
                    {{-- This is just a draft to see if the current page can fit the notes --}}
                    <div class="invoice-notes hidden-notes">
                        @include('offer::pdf.components.notes')
                    </div>

                    <div class="footer-notes invoice-notes">
                        @include('offer::pdf.components.notes')
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
    @include('offer::pdf.components.header', ['offer' => $offer])

</body>
</html>
