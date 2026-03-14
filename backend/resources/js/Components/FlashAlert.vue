<!-- eslint-disable vue/no-v-html -->
<template>
    <v-snackbar
        v-model="show"
        color="primary"
        :timeout="timeout"
        bottom
        left
        dark
        auto-height
    >
        <v-layout items-center>
            <v-icon
                v-if="flashIcon"
                :color="flashColor"
                left
            >
                {{ flashIcon }}
            </v-icon>

            <div
                class="subheading"
                v-html="message"
            />
        </v-layout>
    </v-snackbar>
</template>

<script>
import { isNil } from '@/Composables/useUtils'
import { moduleBuses } from '@/eventBuses'

/**
 * Backend Usage: redirect()->back()->message(type, message)
 * Frontend Usage: this.$root.$emit('flash', {type: 'error', message: 'sample message here'})
 */
export default {
    props: {
        flashData: {
            type: [Object, String, Array],
            default: () => ({}),
            required: false,
        },
    },
    data: () => ({
        show: false,
        type: null,
        message: null,
        timeout: 5000,
    }),

    computed: {
        flashIcon() {
            return {
                success: 'check_circle',
                error: 'error',
                chat: 'chat',
                new_notification: 'notifications_active',
            }[this.type]
        },

        flashColor() {
            return {
                success: 'green darken-1',
                error: 'red lighten-1',
                chat: 'blue lighten-1',
                new_notification: 'blue lighten-1',
            }[this.type]
        },
    },

    watch: {
        show(value) {
            if (value) return

            this.type = null
            this.message = null
        },
        flashData(newFlash) {
            if (newFlash?.type && newFlash?.message) {
                this.flash(newFlash?.type, newFlash?.message)
            }
        },

        '$page.props.errors': {
            handler(errors) {
                if (isNil(errors)) return

                Object.entries(errors).forEach(([field, message]) => {
                    if (field.includes('throw_validation_error')) return

                    this.flash('error', message.replace('_', ' '))
                })
            },
            deep: true,
        },
    },

    mounted() {
        this.$root.$on('flash', ({ type, message, options = {} }) =>
            this.flash(type, message, options),
        )
        this.$root.$on('flash.error', (message) => this.flash('error', message))
        this.$root.$on('flash.validation', (errors) =>
            this.flashValidationError(errors),
        )
        this.$root.$on('flash.success', (message) =>
            this.flash('success', message),
        )

        moduleBuses.forEach((bus) => {
            bus.$on('flash.error', (msg) => this.flash('error', msg))
            bus.$on('flash.success', (msg) => this.flash('success', msg))
            bus.$on('flash.validation', (errors) =>
                this.flashValidationError(errors),
            )
        })
    },

    methods: {
        flash(type, message, options = {}) {
            if (this.show) return

            // Check if options has `timeout` property
            if (options?.timeout) {
                this.timeout = options.timeout
            }

            this.type = type
            this.message = message

            this.$nextTick(() => (this.show = true))
        },

        flashValidationError(errors) {
            this.flash(
                'error',
                Object.values(errors)
                    .map((error) => {
                        if (
                            typeof error === 'string' ||
                            error instanceof String
                        )
                            return error

                        return Object.values(error)
                    })
                    .join('<br />'),
            )
        },
    },
}
</script>
