<template>
    <v-dialog
        v-model="isShow"
        transition="dialog-bottom-transition"
        scrollable
        fullscreen
    >
        <template #activator="props">
            <slot
                v-bind="props"
                name="activator"
            />
        </template>

        <v-card
            ref="notificationCard"
            class="hide-overflow"
            style="position: relative"
            tile
        >
            <v-toolbar
                color="transparent"
                card
            >
                <v-toolbar-title class="mr-auto">
                    {{ $t('notifications.all-notifications') }}
                </v-toolbar-title>

                <v-btn
                    flat
                    icon
                    @click="isShow = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-divider />

            <v-card-text
                ref="notificationsContainer"
                class="pa-0"
            >
                <UserNotifications
                    :notifications="notifications"
                    :is-loading="pagination.isLoading"
                    @click.native="isShow = false"
                />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
// This component is used to show all notifications in a dialog
// when the user clicks on the notifications icon in the header.

import { useInfiniteScroll } from '@vueuse/core'
import axios from 'axios'
import UserNotifications from './UserNotifications.vue'

export default {
    components: {
        UserNotifications,
    },

    data: () => ({
        isShow: false,
        notifications: [],
        pagination: {
            current_page: 0,
            next_page_url: null,
            isLoading: true,
        },
    }),

    watch: {
        isShow(newVal) {
            if (newVal) this.fetch()
        },
    },

    mounted() {
        useInfiniteScroll(
            this.$refs.notificationsContainer,
            () => {
                if (this.pagination.isLoading || !this.pagination.next_page_url)
                    return
                this.fetch(this.pagination.current_page + 1)
            },
            { distance: 10 },
        )
    },

    methods: {
        async fetch(page = 1) {
            try {
                this.pagination.isLoading = true

                if (page == 1) {
                    this.notifications = []
                    this.pagination = {
                        current_page: 0,
                        next_page_url: null,
                        isLoading: true,
                    }
                }

                const {
                    data: { data, current_page, next_page_url },
                } = await axios.get(
                    this.$route('account.notifications.all', { page }),
                )

                this.notifications = [...this.notifications, ...data]
                this.pagination = {
                    current_page,
                    next_page_url,
                    isLoading: false,
                }
            } catch (errors) {
                console.error(errors)
            }
        },
    },
}
</script>
