<script setup>
import { router, useForm, usePage } from '@inertiajs/vue2'
import { computed, toRef } from 'vue'
import ConfirmsAction from '~Shared/Components/ConfirmsAction.vue'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const props = defineProps({
    tile: {
        type: Boolean,
        default: false,
    },
})

const form = useForm({})
const isTile = toRef(props, 'tile')
const { $t } = useGlobalVariables()
const invoice = usePage().props.invoice

const isVisible = invoice?.status?.is_draft

const isDisabled = computed(() => {
    return form.processing || !invoice.status.is_draft
})

const icon = computed(() => {
    return form.recentlySuccessful ? 'done_outline' : 'email'
})

const label = computed(() => {
    return form.recentlySuccessful
        ? 'Sent'
        : $t('invoices.buttons.send-to-client')
})

const handleSendToClient = () => {
    router.post(
        window.route('invoicify.send_to_client', { invoice: invoice.id }),
        {
            onSuccess() {
                router.reload({
                    only: ['invoice'],
                })
            },
            onError: console.warn,
        },
    )
}
</script>

<template>
    <ConfirmsAction
        v-if="isVisible"
        type="info"
        :title="$t('labels.warning')"
        :question="$t('invoices.message.confirm_send_to_client')"
        @confirm="handleSendToClient"
    >
        <template #default="{ on, attrs }">
            <v-list-tile
                v-if="isTile"
                avatar
                :disabled="isDisabled"
                v-bind="attrs"
                v-on="on"
            >
                <v-list-tile-avatar>
                    <v-icon>{{ icon }}</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>{{ label }}</v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>

            <v-btn
                v-else
                depressed
                :disabled="isDisabled"
                :loading="form.processing"
                color="info"
                v-bind="attrs"
                v-on="on"
            >
                <v-icon left>{{ icon }}</v-icon> {{ label }}
            </v-btn>
        </template>
    </ConfirmsAction>
</template>
