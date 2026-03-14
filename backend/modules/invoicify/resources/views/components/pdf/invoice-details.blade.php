@props(['invoice'])

<div style="clear:both;margin-bottom:3mm;">
    <div style="float:left;width:33%;">
        <strong>{{ __('invoices.preview.invoice') }}: </strong> <br> {{ $invoice->invoiceNo }}
    </div>
    <div style="float:left;width:33%;" align="center">
        <strong>{{ __('invoices.preview.order-number') }}: </strong> <br> {{ $invoice->orderNo }}
    </div>
    <div style="float:left;width:33%;" align="right">
        <strong>{{ __('invoices.preview.date') }}: </strong> <br> {{ $invoice->invoiceDate }}
    </div>
</div>

<div style="margin-bottom:3mm;">
    <strong>{{ __('invoices.preview.construction-site') }}: </strong> {{ $invoice->constructionSite }} <br>
    <strong>{{ __('invoices.preview.execution-period') }}: </strong> {{ $invoice->executionPeriod }} <br>
    <strong>{{ __('invoices.preview.service-recipient') }}: </strong> {{ $invoice->serviceRecipient }} {{$invoice->legalForm}} <br>
</div>
