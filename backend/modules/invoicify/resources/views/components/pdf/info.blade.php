@props(['recipient', 'sender', 'careOf'])

<table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:5mm;">
    <tr style="page-break-inside: auto;">
        <td width="85mm" valign="top" style="padding:0; margin:0; border:0; vertical-align:top;">
            <div style="height:16mm;">&nbsp;</div>
            <div style="height:5mm;">
                <small>
                    {{ $sender->name }}
                    {{ $sender->legalForm }}
                    {{ $sender->presentAddress }}
                </small>
            </div>
            <div>
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
        </td>

        <td width="75mm" valign="top" style="padding:0; margin:0; border:0; text-align:right; word-wrap:break-word; overflow-wrap:break-word; white-space:normal;">
            <div style="margin:0 0 3mm 0; text-align:right;">
                @if (filled($sender->logoImgPath))
                    <img src="{{ $sender->logoImgPath }}" height="150" width="150" style="display:inline-block;">
                @endif
            </div>

            <div style="padding:0; margin:0; text-align:right;">
                <strong>{{ $sender->name }} {{ $sender->legalForm}}</strong><br>
                {!! nl2br(e($sender->presentAddress)) !!}
            </div>
        </td>
    </tr>

    <tr style="page-break-inside: auto;">
        <td width="85mm" valign="top" style="padding:0; margin:0; border:0; word-wrap:break-word; overflow-wrap:break-word; white-space:normal;">
            <h2 style="margin:0; padding:0; word-wrap:break-word; overflow-wrap:break-word; white-space:normal;">
                {{ $recipient->additionalNote }}
            </h2>
        </td>
        <td width="75mm" valign="top" align="right" style="padding:0; margin:0; border:0; word-wrap:break-word; overflow-wrap:break-word; white-space:normal;">
            <a href="{{ $sender->websiteUrl }}" style="margin:0; padding:0;">{{ $sender->websiteUrl }}</a><br>
            <a href="mailto:{{ $sender->emailAddress }}" style="margin:0; padding:0;">{{ $sender->emailAddress }}</a><br>
        </td>
    </tr>
</table>
