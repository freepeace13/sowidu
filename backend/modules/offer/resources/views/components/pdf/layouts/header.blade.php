@props(['name'])

<htmlpageheader name="{{ $name }}">
    {{ $slot }}
</htmlpageheader>
