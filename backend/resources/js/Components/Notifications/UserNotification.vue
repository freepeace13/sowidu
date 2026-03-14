<template>
    <v-list-tile
        :class="{ 'grey lighten-4': notification.is_unread }"
        @click="readNotification(notification)"
    >
        <v-list-tile-avatar>
            <img
                :src="causerAvatar"
                @error="setDefaultImage"
            />
        </v-list-tile-avatar>

        <v-list-tile-content>
            <v-list-tile-title>
                <!-- eslint-disable-next-line vue/no-v-html -->
                <span
                    class="body-1"
                    v-html="notification.message"
                />
            </v-list-tile-title>
            <v-list-tile-sub-title class="caption">
                {{ notification.notified_at }}
            </v-list-tile-sub-title>
        </v-list-tile-content>

        <v-list-tile-action v-if="notification.is_unread">
            <v-icon
                color="blue"
                small
            >
                circle
            </v-icon>
        </v-list-tile-action>
    </v-list-tile>
</template>

<script>
export default {
    props: {
        notification: {
            type: Object,
            required: false,
            default: () => ({}),
        },
    },

    computed: {
        causerAvatar() {
            return (
                this.notification?.photo ??
                this.notification?.causer?.photo ??
                this.notification?.causer?.avatar ??
                this.$page.props.defaults.avatars.user
            )
        },
    },

    methods: {
        setDefaultImage(e) {
            e.target.src = this.$page.props.defaults.avatars.user
        },

        readNotification(notification) {
            const url = this.$route('account.notifications.read', {
                notification: notification.id,
            })

            this.$inertia.put(
                url,
                {},
                {
                    only: ['notifications', 'unreadNotifications'],
                    onSuccess: () => this.redirectNotification(notification),
                },
            )
        },

        redirectNotification({ redirectTo }) {
            if (redirectTo) {
                window.open(redirectTo, '_blank')
            }
            // this.$inertia.visit(redirectTo, {
            //     onError: (errors) => {
            //         this.$root.$emit('flash.validation', errors)
            //     },
            // })
        },
    },
}
</script>
