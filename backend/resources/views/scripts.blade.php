{{-- Global configuration object --}}
@php
    $config = [
        'appName' => config('app.name'),
        'appDebug' => env('APP_DEBUG'),
        'avatars' => json_decode(json_encode($avatars), true),
    ];
@endphp

{{-- <script>window.config = {!! json_encode($config) !!}</script> --}}

{{-- Polyfill some features via polyfill.io --}}
@php
    $polyfills = ['Promise', 'Object.assign', 'Object.values', 'Array.prototype.find', 'Array.prototype.findIndex', 'Array.prototype.includes', 'String.prototype.includes', 'String.prototype.startsWith', 'String.prototype.endsWith'];
@endphp
<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features={{ implode(',', $polyfills) }}"></script>

{{-- Load the application scripts --}}
<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="{{ mix('js/app.js') }}"></script>
