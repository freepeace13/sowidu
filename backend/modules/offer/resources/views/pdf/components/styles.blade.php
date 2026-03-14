@php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $cssFile = $manifest['modules/offer/resources/css/pdf.css']['file'] ?? null;
    $cssContent = $cssFile ? file_get_contents(public_path('build/' . $cssFile)) : '';
@endphp

<style>
    {!! $cssContent !!}
</style>

<style>
    /* mPDF has DejaVu Sans built-in, no @font-face needed */
    body {
        font-family: 'dejavusans', sans-serif;
        margin: 0;
        padding: 0;
    }

    @page {
        margin-top: 49.5mm;
        margin-bottom: 74px;
        margin-left: 12.5mm;
        margin-right: 12.5mm;
    }

    @page :first {
        margin-top: 8.3mm !important;
        z-index: 999;
    }



    .invoice-header {
        z-index: 999;
        background-color: white;
    }

    .sticky.invoice-header {
        position: fixed;
        top: -115px;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 1;
    }

    .invoice-body {
        position: relative;
        page-break-inside: auto;
        padding-bottom: 40px;
    }

    .invoice-items-container {
        min-height: 740px;
        page-break-inside: auto;
    }

    .invoice-body table {
        width: 100%;
    }

    .invoice-footer {
        position: fixed;
        bottom: -40px;
        left: 0;
        right: 0;
        width: 100%;
        background-color: white;
        z-index: 1000;
    }

    .notes-section {
        width: 100%;
        position: relative;
    }

    .footer-notes {
        page-break-inside: avoid;
        bottom: 45px;
        position: fixed;
        left: 0;
        right: 0;
    }

    .hidden-notes {
        color: transparent !important;
    }

    .subject-and-description {
        margin-top: 20px;
    }

    .invoice-preview-information {
        margin-top: 12mm !important;
    }
</style>