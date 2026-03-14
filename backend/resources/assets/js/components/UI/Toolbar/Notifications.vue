<template>
    <v-menu
        :close-on-content-click="false"
        nudge-bottom="11"
        max-width="450px"
        nudge-right="-450"
        min-width="450px"
        offset-y
        offset-x
    >
        <v-btn icon slot="activator">
            <v-badge
                class="d-flex"
                right color="red"
                :value="notifications.filter(e => e.isUnread).length"
            >
                <template v-slot:badge>
                    <span>{{ notifications.filter(e => e.isUnread).length }}</span>
                </template>

                <v-icon color="grey darken-1">notifications</v-icon>
            </v-badge>
        </v-btn>

        <v-card color="grey darken-4" min-width="400">
            <v-card-text>Notifications</v-card-text>

            <v-divider></v-divider>

            <div class="notification-panel">
                <v-list class="py-0" three-line dense>
                    <v-list-tile
                        v-for="notification in notifications"
                        :key="notification.id"
                    >
                        <v-list-tile-avatar>
                            <v-img :src="notification.data.avatar" />
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{ notification.data.title }}
                            </v-list-tile-title>
                            <v-list-tile-sub-title v-if="notification.data.subtitle">
                                {{ notification.data.subtitle }}
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </div>
        </v-card>
    </v-menu>
</template>

<script>
export default {
    name: 'NotificationsTray',

    props: {
        notifications: {
            type: Array,
            default: () => ([])
        }
    }
}
</script>