@component('mail::message')
# {{ config('app.name') }}

Hi {{ $order->client?->name ?? $order->client?->full_name }},

<br />
{{ $invoice->subject ?? trans('invoices.mail.send-to-client.mail-intro', [
    'company' => $invoice->company->name,
]) }}
<br />
@if (filled($invoice->description))
    <div>
        {{ $invoice->description }}
    </div>
@endif

@component('mail::panel')
{{ trans('invoices.labels.invoice-details') }}: {{ $invoice->internal_id }} on {{ trans('headings.orders') }}
{{ $order->external_id }}
<br />
{{ trans('invoices.labels.total') }}: {{ $totalAmount }}
@endcomponent

{{ trans('invoices.mail.send-to-client.view-invoice') }}:

@component('mail::button', [
    'url' => route('invoices.preview', [
        'invoice' => $invoice,
    ]),
])
{{ trans('invoices.buttons.preview')}}
@endcomponent

{{ trans('invoices.mail.footer') }},
<br />
{{ ucfirst($invoice->company->name) }}

@endcomponent
