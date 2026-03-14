<template>
    <v-list
        ref="userNotificationsList"
        two-line
        class="pa-0"
    >
        <v-list-tile v-show="!notificationList.length && !isLoading">
            <v-list-tile-content>
                <v-list-tile-title class="tw-text-center">
                    {{ $t('hints.notification-empty') }}
                </v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>

        <UserNotification
            v-for="notification in notificationList"
            :key="notification.id"
            :notification="notification"
        />

        <v-list-tile
            v-for="i in 3"
            v-show="isLoading"
            :key="i"
            class="tw-flex tw-w-full tw-items-center tw-animate-pulse tw-h-16"
            full-width
        >
            <v-list-tile-avatar>
                <v-icon
                    x-large
                    light
                >
                    account_box
                </v-icon>
            </v-list-tile-avatar>

            <v-list-tile-content>
                <v-list-tile-title dense>
                    <div
                        class="tw-w-full tw-h-2 tw-bg-gray-200 tw-rounded-full tw-dark:bg-gray-700"
                    />
                </v-list-tile-title>
                <v-list-tile-sub-title class="caption">
                    <div
                        class="tw-h-2 tw-bg-gray-200 tw-rounded-full tw-dark:bg-gray-700 tw-w-32"
                    />
                </v-list-tile-sub-title>
            </v-list-tile-content>
        </v-list-tile>
    </v-list>
</template>

<script>
import TodoNotificationTypes from '@Todos/Mixins/TodoNotificationTypes'
import UserNotification from './UserNotification.vue'

export default {
    components: { UserNotification },

    mixins: [TodoNotificationTypes],

    props: {
        notifications: {
            type: Array,
            default: () => [],
        },

        isLoading: {
            type: Boolean,
            default: false,
            required: false,
        },
    },

    computed: {
        notificationList() {
            // const notifications = useGetPageProps('notifications', [])
            // return notifications?.map((notification) => {
            //     try {
            //         return this.resolveNotification(notification)
            //     } catch (error) {
            //         return notification
            //     }
            // })
            return this.notifications.map((notification) => {
                try {
                    return this.resolveNotification(notification)
                } catch (error) {
                    return notification
                }
            })
        },
    },

    methods: {
        resolveNotification(notification) {
            return {
                ['App\\Notifications\\NewCompanyInvitation']: (
                    notification,
                ) => {
                    const company = notification.company
                        ? notification.company.name
                        : notification.name
                    const photo = notification.company
                        ? notification.company.photo
                        : notification.photo

                    return {
                        ...notification,
                        photo: photo,
                        message: this.$t('notifications.invitation.created', {
                            company,
                        }),
                        redirectTo: this.$route('account.organizations.index'),
                    }
                },

                ['App\\Notifications\\NewTeamMember']: (notification) => {
                    const { photo, name, ...original } = notification
                    return {
                        photo: notification.photo,
                        message: this.$t('notifications.invitation.accepted', {
                            subject: notification.name,
                        }),
                        redirectTo: this.$route('account.employees.index'),
                        ...original,
                    }
                },
                ...this.todoNotificationTypes(),
            }[notification.type](notification)
        },
    },
}
</script>
