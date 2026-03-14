<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name') }}</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">
        <link rel="stylesheet" href="{{ mix('css/vendor.css') }}">
        {{-- <link rel="stylesheet" href="{{ mix('css/app.css') }}"> --}}
    </head>
    <body>
        @php
            share('app.locale', app()->getLocale());
        @endphp
        <div
            id="app"
            data-misc="{{ json_encode($misc, true) }}"
            data-permissions=" json_encode($permissions, true) "
            data-errors="{{ json_encode($errors->toArray(), true) }}"
        ></div>

        @shared

        @include('scripts')
    </body>
</html>
