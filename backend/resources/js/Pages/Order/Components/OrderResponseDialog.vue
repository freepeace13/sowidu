<template>
    <v-alert
        :value="true"
        :color="dialog?.color ?? 'warning'"
        outline
    >
        <div
            class="tw-flex tw-flex-col md:tw-flex-row tw-items-center tw-justify-between"
        >
            <div class="tw-text-lg tw-flex tw-items-center tw-w-full">
                <VIcon
                    large
                    :color="dialog?.color ?? 'warning'"
                    class="mr-3"
                >
                    {{ dialog?.icon ?? 'warning' }}
                </VIcon>

                {{ dialog.message }}
            </div>

            <div
                class="tw-flex tw-flex-col md:tw-flex-row tw-w-full md:tw-justify-end"
            >
                <v-btn
                    v-if="dialog?.accept_button_label"
                    :color="dialog?.color ?? 'primary'"
                    :class="acceptButtonClass"
                    @click="confirmAccepting"
                >
                    {{ dialog.accept_button_label }}
                </v-btn>
                <v-btn
                    v-if="dialog.cancelable"
                    color="error"
                    @click="cancel"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-btn
                    v-if="dialog?.rejectable"
                    color="error"
                    @click="reject"
                >
                    {{ $t('order.buttons.reject') }}
                </v-btn>
                <v-btn
                    v-if="dialog?.force_confirm"
                    color="warning"
                    @click="confirmForForceAcceptance"
                >
                    {{
                        dialog?.force_confirm_button_label ??
                        $t('order.buttons.force-confirm')
                    }}
                </v-btn>
            </div>
        </div>
    </v-alert>
</template>

<script>
export default {
    props: {
        dialog: {
            type: Object,
            default: () => ({}),
            required: false,
        },
        action: {
            required: false,
            type: Object,
            default: () => ({}),
        },
        isShow: {
            required: false,
            type: Boolean,
            default: false,
        },
        order: {
            required: true,
            type: Number,
        },
    },

    computed: {
        acceptButtonClass() {
            return this.hexToContrast(this.dialog?.color)
        },
    },

    methods: {
        hexToContrast(hexColor) {
            if (!hexColor) return 'black--text'

            const hex = hexColor.replace(/#/, '')
            const r = parseInt(hex.substr(0, 2), 16)
            const g = parseInt(hex.substr(2, 2), 16)
            const b = parseInt(hex.substr(4, 2), 16)

            return [0.299 * r, 0.587 * g, 0.114 * b].reduce((a, b) => a + b) /
                255 <
                0.5
                ? 'white--text'
                : 'black--text'
        },

        confirmAccepting() {
            const { value } = this.action

            // If the action is `Start Working`
            if (value === 3) {
                const order = this.order
                this.$confirm.ask({
                    title: 'Confirm',
                    question: 'Do you want to start time tracker?',
                    type: 'warning',
                    confirm: () => {
                        this.$inertia.patch(
                            this.$route('orders.time_tracker.start', { order }),
                            {},
                            {
                                preserveScroll: true,
                                only: [
                                    'order',
                                    'requiresResponse',
                                    'flash',
                                    'timeLogs',
                                    'errors',
                                    'timeTrack',
                                ],
                                onSuccess: () => {
                                    this.$root.$emit(
                                        'flash.success',
                                        'Time tracker stared.',
                                    )
                                },
                                onError: (error) => {
                                    this.$root.$emit('flash.validation', error)
                                },
                            },
                        )
                    },
                })

                return
            }

            this.$confirm.ask({
                title: 'Confirm',
                question:
                    'Are you sure about this? This action cannot be undone.',
                confirm: () => {
                    this.accept(value)
                },
            })
        },

        accept(value) {
            const order = this.order

            this.$inertia.patch(
                this.$route('orders.accept.response', { order }),
                {
                    value,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['order', 'requiresResponse', 'flash', 'errors'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Order has been accepted.',
                        )
                    },
                    onError: (error) => {
                        this.$root.$emit('flash.validation', error)
                    },
                },
            )
        },

        cancel() {
            const order = this.order

            this.$confirm.ask({
                title: 'Cancel',
                question:
                    'Are you sure you want to cancel this order? This action cannot be undone.',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('orders.reject.response', { order }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['order', 'requiresResponse'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Order has been cancelled.',
                                )
                            },
                        },
                    )
                },
            })
        },

        reject() {
            const order = this.order

            this.$confirm.ask({
                title: 'Reject',
                question:
                    'Are you sure you want to reject this order? This action cannot be undone.',
                type: 'delete',
                confirm: () => {
                    this.$inertia.patch(
                        this.$route('orders.disapprove.response', { order }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['order', 'requiresResponse'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'You reject this order outcome.',
                                )
                            },
                        },
                    )
                },
            })
        },

        confirmForForceAcceptance() {
            const { value } = this.action

            this.$confirm.ask({
                title: 'Warning',
                question: this.$t(
                    'order.status.response.message.warning-before-force-order-confirmation',
                ),
                type: 'warning',
                confirm: () => {
                    this.accept(value)
                },
            })
        },
    },
}
</script>
