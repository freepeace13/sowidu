@props([
    'offer',
    'recipient',
    'items',
    'totals',
    'companyLogoPath',
    'companyInvoiceDefaults',
])

<x-offer::pdf.layouts.base>
    <x-slot:title>{{ __('offer.labels.offer') }} - {{ $offer->internal_id }}</x-slot>

    <x-slot:header>
        <x-offer::pdf.page-header :image="$companyLogoPath" />
    </x-slot:header>

    <x-slot:footer>
        <x-offer::pdf.page-footer
            :company-invoice-defaults="$companyInvoiceDefaults"
            :company="$offer->company"
        />
    </x-slot:footer>

    <x-offer::pdf.info
        :offer="$offer"
        :recipient="$recipient"
        :company-logo-path="$companyLogoPath"
        :company-invoice-defaults="$companyInvoiceDefaults"
    />

    <x-offer::pdf.offer-details :offer="$offer" :recipient="$recipient" />

    @if (filled($offer->subject) || filled($offer->message))
        <div style="margin-bottom:3mm;">
            <strong>{{ $offer->subject }}</strong>
            <div style="font-size:10pt;text-align:justify;">
                <i>{{ $offer->message }}</i>
            </div>
        </div>
    @endif

    <x-offer::pdf.items-table :items="$items" />

    <div style="page-break-inside: avoid;">
        <x-offer::pdf.total-summary :totals="$totals" />
    </div>

    @if ($offer->notes)
        <div style="position:absolute;bottom:35mm;left:15mm;right:15mm;">
            <div style="line-height:normal;margin-bottom:2px;">
                {{ $offer->notes }}
            </div>
        </div>
    @endif
</x-offer::pdf.layouts.base>
