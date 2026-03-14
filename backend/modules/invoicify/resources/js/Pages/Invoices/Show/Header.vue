<script setup>
import AddPaymentButton from '~Invoicify/Components/Actions/AddPaymentButton.vue'
import DeleteInvoiceButton from '~Invoicify/Components/Actions/DeleteInvoiceButton.vue'
import PreviewButton from '~Invoicify/Components/Actions/PreviewButton.vue'
import SendToClientButton from '~Invoicify/Components/Actions/SendToClientButton.vue'
</script>

<template>
    <v-toolbar
        color="white"
        flat
    >
        <v-toolbar-title class="title tw-flex tw-items-center">
            <div class="md:tw-text-xl tw-text-lg">
                <v-btn
                    icon
                    @click="$inertia.get($route('invoices.index'))"
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>
                {{ $t('invoices.labels.invoice-details') }}
            </div>
        </v-toolbar-title>
        <v-spacer />
        <PreviewButton />
        <SendToClientButton />
        <AddPaymentButton />
        <DeleteInvoiceButton />
        <!--
        <v-btn
            v-if="invoice.is_paid"
            color="success"
            depressed
        >
            <v-icon left>done_outline</v-icon>
            {{ $t('invoices.labels.paid') }}
        </v-btn> -->
    </v-toolbar>
</template>
