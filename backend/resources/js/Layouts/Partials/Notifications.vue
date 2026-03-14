<template>
    <v-menu
        v-model="show"
        offset-y
        z-index="12"
        allow-overflow
        max-height="70%"
        :close-on-content-click="false"
        :min-width="
            $vuetify.breakpoint.xs ? $vuetify.breakpoint.width - 48 : 450
        "
        right
    >
        <template #activator="{ on }">
            <v-btn
                icon
                v-on="on"
            >
                <v-badge
                    color="red"
                    :value="unreadNotifications > 0"
                >
                    <template #badge>
                        <span>{{ unreadNotifications }}</span>
                    </template>
                    <v-icon>notifications</v-icon>
                </v-badge>
            </v-btn>
        </template>

        <v-card :width="$vuetify.breakpoint.xs ? 'auto' : 450">
            <v-card-title class="subheading tw-flex tw-justify-between">
                {{ $t('headings.notifications') }}

                <div
                    :class="[
                        'body-2',
                        {
                            disabled: !notifications.length,
                            'tw-cursor-pointer hover:tw-underline':
                                notifications.length,
                        },
                    ]"
                    @click="readAll"
                >
                    {{ $t('notifications.mark-all-as-read') }}
                </div>
            </v-card-title>

            <v-divider />

            <UserNotifications
                :notifications="notifications"
                :is-loading="isLoading"
            />

            <v-divider />

            <v-card-actions
                class="pa-3 tw-justify-center tw-cursor-pointer hover:tw-underline"
            >
                <UserNotificationsDialog>
                    <template #activator="{ on }">
                        <div v-on="on">
                            {{ $t('notifications.show-all-notifications') }}
                        </div>
                    </template>
                </UserNotificationsDialog>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script>
// This component is responsible for displaying the notifications menu in the header.

import UserNotificationsDialog from '@/Components/Notifications/UserNotificationsDialog.vue'
import UserNotifications from '../../Components/Notifications/UserNotifications.vue'

export default {
    components: {
        UserNotifications,
        UserNotificationsDialog,
    },

    props: {
        unreadNotifications: {
            type: Number,
            default: 0,
        },
    },

    data: () => ({
        notifications: [],
        isLoading: false,
        show: false,
    }),

    watch: {
        show(newVal) {
            if (newVal) {
                this.showNotificationMenu()
            }
        },
    },

    methods: {
        showNotificationMenu() {
            this.isLoading = true
            this.$inertia.reload({
                only: ['notifications', 'unreadNotifications'],
                onSuccess: ({ props: { notifications } }) => {
                    this.notifications = notifications
                },
                onFinish: () => (this.isLoading = false),
            })
        },

        readAll() {
            this.$inertia.post(this.$route('account.notifications.read_all'), {
                preserveState: false,
                preserveScroll: true,
                only: ['notifications', 'unreadNotifications'],
            })
        },
    },
}
</script>
