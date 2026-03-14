@props(['name'])

<htmlpagefooter name="{{ $name }}">
    {{ $slot }}
    <div align="center" style="margin-top:2mm;">
        <small>Page {PAGENO} of {nbpg}</small>
    </div>
</htmlpagefooter>
