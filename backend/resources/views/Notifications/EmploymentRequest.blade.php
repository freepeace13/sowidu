@php $payload = $notification->data['payload']; @endphp
<div role="listitem" class="grey darken-{{ $notification->unread() ? '3' : '4' }} pa-2">
    <div class="v-list__tile v-list__tile--avatar theme--dark">
        <div class="v-list__tile__avatar">
            <div class="v-avatar" style="height: 40px; width: 40px;">
                <img src="{{ $payload['employee']['avatar'] }}" alt="">
            </div>
        </div>
        <div class="v-list__tile__content">
            {{ $payload['employee']['name'] }} requesting you to be an employee of {{ $payload['company'] }}.

            <small class="mt-3">
                {{ $notification->created_at->diffForHumans() }}
            </small>
        </div>
    </div>
</div>
