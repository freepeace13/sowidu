@props(['image'])

<x-offer::pdf.layouts.header name="defaultHeader">
    <div style="clear:both;">
        <div style="float:left;width:85mm;">&nbsp;</div>
        <div style="float:left;width:75mm;margin-left:20mm;">
            @if(filled($image))
                <div align="right" style="margin-bottom:3mm;">
                    <img src="{{ $image }}" height="150" width="150" vertical-align="middle">
                </div>
            @endif
        </div>
    </div>
</x-offer::pdf.layouts.header>
