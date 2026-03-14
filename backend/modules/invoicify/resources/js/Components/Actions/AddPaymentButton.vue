<script setup>
import { usePage } from '@inertiajs/vue2'
import { useEventBus } from '@vueuse/core'
import { toRef } from 'vue'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const props = defineProps({
    tile: {
        type: Boolean,
        default: false,
    },
})

const isTile = toRef(props, 'tile')
const { $t } = useGlobalVariables()
const invoice = usePage().props.invoice

const paymentFormEmitter = useEventBus('invoice.payments.form.show')

const isVisible = invoice && !invoice.status?.is_draft && !invoice.is_paids

const showAddPaymentForm = () => {
    paymentFormEmitter.emit('invoice.payments.form.show')
}
</script>
<template>
    <div v-if="isVisible">
        <v-list-tile
            v-if="isTile"
            avatar
            :disabled="invoice?.is_paid"
            @click="showAddPaymentForm"
        >
            <v-list-tile-avatar>
                <v-icon>payments</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-content>
                <v-list-tile-title>
                    {{ $t('invoices.buttons.add_payment') }}
                </v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>
        <v-btn
            v-else
            depressed
            color="blue-info"
            :disabled="invoice?.is_paid"
            :loading="false"
            class="white--text"
            @click="showAddPaymentForm"
        >
            <v-icon left>payments</v-icon>
            {{ $t('invoices.buttons.add_payment') }}
        </v-btn>
    </div>
</template>
