<template>
    <v-layout
        row
        align-center
        justify-start
        fill-height
        class="border-top-primary"
    >
        <div
            class="tw-flex -tw-space-x-1 xs:tw-space-x-1 tw-overflow-hidden tw-mr-2 tw-items-center tw-h-full px-1"
        >
            <Subscriber
                v-for="subscriber in subscribers"
                :key="subscriber.id"
                :avatar="subscriber.user.photo"
                :name="`${subscriber.user.name} (${subscriber.role})`"
                @click.native="
                    (e) => $emit('click:subscriber', { e, subscriber })
                "
            />
            <BoardSubscribersMenu :subscribers="subscribers" />
        </div>
    </v-layout>
</template>
<script>
import Subscriber from '../Subscriber/Subscriber.vue'
import BoardSubscribersMenu from '../Subscriber/BoardSubscribersMenu.vue'

export default {
    components: {
        Subscriber,
        BoardSubscribersMenu,
    },

    props: {
        subscribers: {
            type: Array,
            default: () => [],
        },
    },
}
</script>
