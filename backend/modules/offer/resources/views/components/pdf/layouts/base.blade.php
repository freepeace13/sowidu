@props([
    'title' => 'Offer',
    'locale' => 'en',
    'header' => null,
    'footer' => null,
    'author' => null,
    'keywords' => null,
    'subject' => null,
])

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            body {
                margin: 0;
                line-height: 1.5;
            }

            @page {
                margin-top: 55mm;
                margin-bottom: 34mm;
                header: html_defaultHeader;
                footer: html_defaultFooter;
            }

            @page :first {
                margin-top: 10mm;
                header: _blank;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                vertical-align: bottom;
            }

            table th {
                white-space: nowrap;
                background-color: whitesmoke;
            }

            table td, table th {
                padding: 10px 8px;
                border: 1px solid #ddd;
                vertical-align: top;
            }

            table.simple th, table.simple td {
                border: 0;
                padding: 4px 2px;
            }

            table.borderless th, table.borderless td {
                border: 0;
            }

            htmlpageheader,
            htmlpagefooter {
                display: none;
            }

            .nowrap {
                white-space: nowrap;
            }
        </style>
    </head>
    <body>
        {{ $header ?? '' }}
        {{ $footer ?? '' }}

        {{ $slot }}
    </body>
</html>
