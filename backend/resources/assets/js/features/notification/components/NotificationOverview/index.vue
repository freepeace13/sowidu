<template>
    <v-menu
        :close-on-content-click="false"
        nudge-bottom="16"
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
                :value="unreadNotifications.length"
            >
                <template v-slot:badge>
                    <span>{{ unreadNotifications.length }}</span>
                </template>

                <v-icon :color="iconColor">notifications</v-icon>
            </v-badge>
        </v-btn>

        <v-card color="grey darken-4" min-width="400">
            <v-card-text>Notifications</v-card-text>

            <v-divider></v-divider>

            <div class="notification-panel">
                <v-list class="py-0" three-line dense>
                    <v-list-tile
                        v-for="notification in unreadNotifications"
                        :key="notification.id"
                        @click="$emit('read-notification', notification)"
                        :class="{ 'grey darken-4': !notification.isUnread }"
                    >
                        <v-list-tile-avatar>
                            <v-img :src="notification.avatar" />
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{ notification.message }}
                            </v-list-tile-title>
                            <v-list-tile-sub-title>
                                {{ notification.createdAt | timepast }}
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </div>
        </v-card>
    </v-menu>
</template>

<script>
import moment from 'moment';

export default {
    name: 'NotificationOverview',

    filters: {
        timepast: (date) => moment(date).fromNow()
    },

    props: {
        iconColor: {
            type: String,
            default: 'grey darken-1'
        },

        unreadNotifications: {
            type: Array,
            required: true,
            validator(prop) {
                return prop.every((item) => item.isUnread);
            }
        }
    }
}
</script>