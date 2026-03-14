@props(['offer', 'recipient', 'companyLogoPath', 'companyInvoiceDefaults'])

<table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:5mm;">
    <tr style="page-break-inside: auto;">
        <td width="85mm" valign="top" style="padding:0; margin:0; border:0; vertical-align:top;">
            <div style="height:16mm;">&nbsp;</div>
            <div style="height:5mm;">
                <small>
                    {{ $offer->company->name }}
                    {{ $offer->company->legal_form->legal_form ?? '' }}
                    {{ $offer->company->address->short_full_address ?? '' }}
                </small>
            </div>
            <div style="height:12.7mm;">
                <strong>{{ $recipient->name }} {{ $recipient->legal_form ?? $recipient?->legal_form?->legal_form ?? '' }}</strong><br>
                {!! nl2br(e(join(', ', array_filter([$recipient->address->street ?? '', $recipient->address->house_number ?? ''])) . "\n" . ($recipient->address->zipcode ?? '') . ' ' . ($recipient->address->city ?? ''))) !!}
            </div>

            <div style="height:27.3mm; margin-top: 60px; margin-bottom: -100px;">
                <h2>{{ __('offer.labels.offer') }}</h2>
            </div>
        </td>

        <td width="75mm" valign="top" style="padding:0; margin:0; border:0; text-align:right; word-wrap:break-word; overflow-wrap:break-word; white-space:normal;">
            <div align="right" style="margin-bottom:3mm;height:150px;">
                @if(filled($companyLogoPath))
                    <img src="{{ $companyLogoPath }}" height="150" width="150" style="vertical-align:middle;">
                @endif
            </div>
            <div align="right">
                <strong>{{ $offer->company->name }} {{ $offer->company->legal_form->legal_form ?? '' }}</strong><br>
                {!! nl2br(e(join(', ', array_filter([$offer->company->address->street ?? '', $offer->company->address->house_number ?? ''])) . "\n" . ($offer->company->address->zipcode ?? '') . ' ' . ($offer->company->address->city ?? ''))) !!}
            </div>
            <div align="right">
                <a href="{{ $companyInvoiceDefaults->website ?? '' }}">{{ $companyInvoiceDefaults->website ?? '' }}</a><br>
                <a href="mailto:{{ $companyInvoiceDefaults->company_email ?? '' }}">{{ $companyInvoiceDefaults->company_email ?? '' }}</a>
            </div>
        </td>
    </tr>
</table>
