<script setup>
import { router, usePage } from '@inertiajs/vue2'
import { ref } from 'vue'
import ManualItemForm from '~Invoicify/Components/Forms/ManualItemForm.vue'
import { useEventListener } from '~Invoicify/Composables/useEventListener'
import Events from '~Invoicify/Enums/Events'
import Header from './Header.vue'

const invoice = usePage().props.invoice

const manualItemFormRef = ref(null)
const invoiceTaxFormRef = ref(null)
const invoicePaymentDateFormRef = ref(null)

const handleReload = () => {
    router.reload({ only: ['invoice', 'items', 'invoiceSummary'] })
}

const showManualItemForm = (payload) => {
    if (payload.invoice === invoice.id) manualItemFormRef.value?.show()
}

useEventListener(Events.TOGGLE_MANUAL_ITEM_FORM, showManualItemForm)
</script>

<template>
    <div id="invoicify-invoices-show">
        <ManualItemForm
            ref="manualItemFormRef"
            :invoice="invoice"
            @refresh="handleReload"
        />
        <InvoiceTaxForm ref="invoiceTaxFormRef" />
        <InvoicePaymentDateForm ref="invoicePaymentDateFormRef" />
        <Header />
        <v-divider />
        <v-container fluid>
            <slot />
        </v-container>
        <portal-target name="preview-dialog" />
        <portal-target name="manual-item-dialog" />
        <portal-target name="edit-pdf-properties-dialog" />
        <portal-target name="edit-invoice-details-dialog" />
    </div>
</template>
