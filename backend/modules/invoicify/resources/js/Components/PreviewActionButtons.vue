<script setup>
import AddManualItemButton from './Actions/AddManualItemButton.vue'
import CopyLinkButton from './Actions/CopyLinkButton.vue'
import DownloadAsPdfButton from './Actions/DownloadAsPdfButton.vue'
import EditInvoiceDetailsButton from './Actions/EditInvoiceDetailsButton.vue'
import EditPdfPropertiesButton from './Actions/EditPdfPropertiesButton.vue'
import SendToClientButton from './Actions/SendToClientButton.vue'
import EditInvoiceNotesButton from './Actions/EditInvoiceNotesButton.vue'

defineProps({
    invoice: {
        type: Object,
        required: true,
        validator: (value) => {
            return (
                value &&
                typeof value.id === 'number' &&
                typeof value.is_paid === 'boolean'
            )
        },
    },
})
</script>

<template>
    <v-menu
        left
        offset-y
    >
        <template #activator="{ on, attrs }">
            <v-btn
                v-bind="attrs"
                flat
                icon
                v-on="on"
            >
                <v-icon>more_vert</v-icon>
            </v-btn>
        </template>
        <v-list>
            <EditPdfPropertiesButton
                :invoice="invoice"
                tile
            />
            <EditInvoiceNotesButton
                :invoice="invoice"
                tile
            />
            <CopyLinkButton tile />
            <DownloadAsPdfButton
                :invoice="invoice"
                tile
            />
            <AddManualItemButton tile />
            <EditInvoiceDetailsButton
                :invoice="invoice"
                tile
            />
            <SendToClientButton tile />
        </v-list>
    </v-menu>
</template>
