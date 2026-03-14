<script setup>
import { usePage } from '@inertiajs/vue2'
import Events from '~Invoicify/Enums/Events'
import { EventBus } from '~Invoicify/Services/EventBus'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const { $t } = useGlobalVariables()

const { invoice } = usePage().props

const showInvoiceItemForm = () => {
    EventBus.$emit(Events.TOGGLE_ITEM_FORM, {
        invoice: invoice.id,
    })
}
</script>

<template>
    <v-btn
        v-if="invoice.can_be_edited"
        color="info"
        @click="showInvoiceItemForm"
    >
        {{ $t('invoices.labels.add-item') }}
    </v-btn>
</template>
