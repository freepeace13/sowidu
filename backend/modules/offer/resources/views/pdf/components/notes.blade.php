@if ($offer->notes)
    <div class="notes">
        {!! nl2br(e($offer->notes)) !!}
    </div>
@endif