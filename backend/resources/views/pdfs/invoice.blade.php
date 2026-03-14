<!-- TODO - not used! -->
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @if (isset($testLayout) && $testLayout)
        @vite('resources/css/views/invoice-pdf.css')
    @else
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp

        <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/views/invoice-pdf.css']['file']) }}">

    @endif

    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-size: 0.5em;
            src: url('{{asset('fonts/DejaVuSans.ttf')}}') format("truetype");
        }

        body {
            font-family: 'DejaVu Sans';
        }
    </style>
</head>

<body>
    {!! $html !!}
</body>

</html>
