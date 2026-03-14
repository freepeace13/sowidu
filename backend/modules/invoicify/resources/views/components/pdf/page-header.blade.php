@props(['image'])

<x-invoicify::pdf.layouts.header name="defaultHeader">
    <div style="clear:both;">
        <div style="float:left;width:85mm;">&nbsp;</div>
        <div style="float:left;width:75mm;margin-left:20mm;">
            <div align="right" style="margin-bottom:3mm;">
                <img src="{{ $image }}" height="150" width="150" vertical-align="middle">
            </div>
        </div>
    </div>
</x-invoicify::pdf.layouts.header>
