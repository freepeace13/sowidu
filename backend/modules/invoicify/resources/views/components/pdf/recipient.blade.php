@props(['recipient', 'sender', 'careOf'])

<div style="float:left;width:85mm;">
    <div style="height:16mm;">&nbsp;</div>
    <div style="height:5mm;">
        <small>
            {{ $sender->name }}
            {{ $sender->legalForm }}
            {{ $sender->presentAddress }}
        </small>
    </div>
    <div style="height:12.7mm;">
        <strong>{{ $recipient->name }} {{ $recipient->legalForm }}</strong><br>
       @if($careOf['careOfName'] || $careOf['careOfLegalForm'] || $careOf['careOfAddress'])
            @if($careOf['careOfName']) {{ $careOf['careOfName'] }}, @endif
            @if($careOf['careOfLegalForm']) {{ $careOf['careOfLegalForm'] }}, @endif
            @if($careOf['careOfAddress']) {!! $careOf['careOfAddress'] !!} @endif
            <br>
        @else
            {!! nl2br(e($recipient->returnAddress)) !!}
        @endif
    </div>

    <div style="height:27.3mm; margin-top: 60px; margin-bottom: -100px;">
        <h2>{{ $recipient->additionalNote }}</h2>
    </div>
</div>
