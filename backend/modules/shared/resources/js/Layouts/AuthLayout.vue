<template>
    <v-app class="!tw-bg-bg-grey">
        <template v-if="!user.locked">
            <v-toolbar
                color="primary"
                clipped-left
                tabs
                flat
                fixed
                dark
                app
                class="tw-z-[11] main-toolbar no-print"
                :dense="$vuetify.breakpoint.smAndDown"
            >
                <Link
                    href="/"
                    class="v-toolbar__title tw-mr-20 white--text hidden-xs-only"
                    style="text-decoration: none"
                >
                    <img
                        :src="require('@/Images/logo.png')"
                        :alt="appName"
                        class="tw-w-12 tw-h-12"
                    />
                </Link>

                <v-spacer v-if="$vuetify.breakpoint.smAndUp" />

                <Account
                    v-if="!isGuest"
                    ref="accountRef"
                    v-bind="$props"
                />

                <v-spacer />

                <v-toolbar-items v-if="!isGuest">
                    <ConfirmationDialog
                        cancel-text="No"
                        confirm-text="Yes"
                        @confirm="lockScreenStart()"
                    >
                        <template #activator="{ on }">
                            <v-btn
                                icon
                                v-on="on"
                            >
                                <v-icon> lock </v-icon>
                            </v-btn>
                        </template>

                        <template #header>
                            <h3>{{ $t('buttons.lockscreen') }}</h3>
                        </template>

                        <template #body>
                            <p>{{ $t('messages.confirm-lockscreen') }}</p>
                        </template>
                    </ConfirmationDialog>

                    <Notifications
                        :unread-notifications="unreadNotifications"
                    />
                    <Apps />
                </v-toolbar-items>

                <!-- @todo check if used -->
                <portal-target name="toplevelsearch" />
                <ConfirmationDialogPlugin />
            </v-toolbar>

            <slot name="sidenav" />

            <!-- fluid  -->
            <v-content
                fluid
                class="!tw-pt-theme-5 md:!tw-pt-theme-6"
            >
                <FlashAlert
                    ref="alerts"
                    :flash-data="flash"
                />

                <slot />

                <ExportInvoiceProgress />
            </v-content>
        </template>

        <template v-if="user.locked">
            <v-content>
                <LockScreen v-bind="user" />
            </v-content>
        </template>
        <HeadTitle :title="title" />
    </v-app>
</template>

<script>
import { Head as HeadTitle } from '@inertiajs/vue2'
import SharedData from '@/Mixins/SharedData'
import { Link } from '@inertiajs/vue2'
import ConfirmationDialog from '../Components/ConfirmationDialog.vue'
import FlashAlert from '../Components/FlashAlert.vue'
import LockScreen from '../Components/LockScreen.vue'
import Account from './Partials/Account.vue'
import Apps from './Partials/Apps.vue'
import Notifications from './Partials/Notifications.vue'
import { router } from '@inertiajs/vue2'
import ExportInvoiceProgress from '@/Pages/Invoice/Components/ExportInvoiceProgress.vue'

export default {
    components: {
        Apps,
        Account,
        Notifications,
        ConfirmationDialog,
        LockScreen,
        FlashAlert,
        HeadTitle,
        // eslint-disable-next-line vue/no-reserved-component-names
        Link,
        ExportInvoiceProgress,
    },

    mixins: [SharedData],

    props: {
        title: {
            required: false,
            type: String,
            default: 'Sowidu',
        },
        isGuest: {
            required: false,
            type: Boolean,
            default: false,
        },
    },

    created() {
        const user = this?.user

        if (!user || user?.isGuest) return

        this.getEchoChannel(user).notification((notification) => {
            this.$inertia.reload({ only: ['unreadNotifications'] })

            this.playNewNotification()

            this.$root.$emit('notification.new', notification)

            const { message } = notification
            const timeout = notification?.timeout ?? 5000

            this.$root.$emit('flash', {
                type: 'new_notification',
                message,
                options: { timeout },
            })
        })

        this.listeningToChat(user)

        // Added this one so we don't add custom headers anymore on every request that has broadcasting events
        this.routerListener = router.on('before', (ev) => {
            ev.detail.visit.headers['X-Socket-Id'] = window.Echo.socketId()
        })
    },

    beforeDestroy() {
        this.routerListener()
    },

    mounted() {
        const flash = this.flash

        if (
            Object.hasOwnProperty.call(flash, 'type') &&
            Object.hasOwnProperty.call(flash, 'message')
        ) {
            this.$refs.alerts.flash(flash.type, flash.message)
        }
    },

    methods: {
        async playNewNotification() {
            try {
                const audio = new Audio('/storage/popup_notification.wav')
                await audio.play()
                // eslint-disable-next-line no-empty
            } catch (error) {}
        },

        lockScreenStart() {
            this.$inertia.post(this.$route('lockscreen.activate'))
        },

        getEchoChannel(user) {
            Object.keys(window.Echo.connector.channels).forEach((name) =>
                window.Echo.leave(name.replace('private-', '')),
            )

            if (user.impersonating) {
                return window.Echo.private(
                    `App.Models.Employee.${user.impersonatorId}`,
                )
            } else {
                return window.Echo.private(`App.Models.User.${user.id}`)
            }
        },

        // @todo moved to `Chat` directory
        listeningToChat(user) {
            if (!this.$route().current('chat.*')) return

            let userId = user.impersonating ? user.impersonatorId : user.id

            window.Echo.private(`chat.conversation.${userId}`)
                .listen('Chat\\MessageSent', ({ message }) => {
                    this.$root.$emit('chat-listener', {
                        event: 'MessageSent',
                        message,
                    })
                })
                .listen('Chat\\MessageUpdated', ({ message }) => {
                    this.$root.$emit('chat-listener', {
                        event: 'MessageUpdated',
                        message,
                    })
                })
                .listen('Chat\\MessageDeleted', ({ message }) => {
                    this.$root.$emit('chat-listener', {
                        event: 'MessageDeleted',
                        message,
                    })
                })
        },
    },
}
</script>
