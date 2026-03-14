@php $payload = $notification->data['payload']; @endphp
<div role="listitem" class="grey darken-{{ $notification->unread() ? '3' : '4' }} pa-2">
    <div class="v-list__tile v-list__tile--avatar theme--dark">
        <div class="v-list__tile__avatar">
            <div class="v-avatar" style="height: 40px; width: 40px;">
                <img src="{{ $payload['avatar'] }}" alt="">
                <!-- <i aria-hidden="true" class="v-icon material-icons theme--dark">notifications</i> -->
            </div>
        </div>
        <div class="v-list__tile__content">
            {{ $payload['name'] }} is accepted your contact request.

            <small class="mt-3">
                {{ $notification->created_at->diffForHumans() }}
            </small>
        </div>
    </div>
</div>
