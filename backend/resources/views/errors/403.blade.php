<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name') }} - 403</title>


    @vite('resources/css/tailwind.css')
</head>

<body class="tw-d-flex tw-flex-col tw-h-screen" style="background-color: #c0e8ff;">
    
    <div class="tw-w-1/2 tw-mx-auto tw-flex-1 tw-flex tw-justify-center tw-items-center tw-flex-col tw-h-full">
        <div class="tw-mb-[4rem]">
            <x-logo />
        </div>
        <img src="{{ asset('/images/errors/403.webp')}}" />
        <h2>403 Forbidden</h2>
        <h3>You’re not allowed to access this page.</h3>
        <a href="{{ route('home') }}" class="tw-bg-[#006686] tw-text-white tw-text-xl tw-no-underline tw-rounded-full tw-font-sans tw-py-4 tw-flex tw-px-8">Back to Home</a>
    </div>

</body>

</html>
