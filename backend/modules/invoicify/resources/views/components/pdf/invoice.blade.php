<x-invoicify::pdf.layouts.base
    :author="$metadata?->author"
    :keywords="$metadata?->keywords"
    :subject="$metadata?->subject"
>
    <x-slot:title>{{ $metadata?->title }}</x-slot>

    <x-slot:header>
        <x-invoicify::pdf.page-header :image="$sender->logoImgPath" />
    </x-slot:header>

    <x-slot:footer>
        <x-invoicify::pdf.page-footer :payment-details="$paymentDetails" />
    </x-slot:footer>

    <x-invoicify::pdf.info :recipient="$recipient" :sender="$sender" :care-of="$careOf" />

    <x-invoicify::pdf.invoice-details :invoice="$invoiceDetails" />

    <div style="margin-bottom:3mm;">
        <strong>{{ $itemTable->title }}</strong>
        <div style="font-size:10pt;text-align:justify;">
            <i>{{ $itemTable->caption }}</i>
        </div>
    </div>

    <x-invoicify::pdf.items-table :item-table="$itemTable" />

    <div style="page-break-inside: avoid;">
        <x-invoicify::pdf.total-summary :totals-summary="$itemTable->totalsSummary" />
    </div>

    <x-invoicify::pdf.closing
        :remarks="$closingBlock->closingRemarks"
        :payment-date="$closingBlock->paymentDate"
        :total-wage-costs="$closingBlock->totalWageCosts"
    />
</x-invoicify::pdf.layouts.base>
