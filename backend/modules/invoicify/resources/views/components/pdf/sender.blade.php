@props(['sender'])

<div style="float:left;width:75mm;margin-left:20mm;">
    <div align="right" style="margin-bottom:3mm;height:150px;">
        @if (filled($sender->logoImgPath))
            <img src="{{ $sender->logoImgPath }}" height="150" width="150" vertical-align="middle">
        @endif
    </div>
    <div align="right">
        <strong>{{ $sender->name }} {{ $sender->legalForm}}</strong> <br>
        {!! nl2br(e($sender->presentAddress)) !!}
    </div>
    <div align="right">
        <a href="{{ $sender->websiteUrl }}">{{ $sender->websiteUrl }}</a><br>
        <a href="mailto:{{ $sender->emailAddress }}">{{ $sender->emailAddress }}</a><br>
    </div>
</div>
