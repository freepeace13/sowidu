<script setup>
import { usePage } from '@inertiajs/vue2'
import { authCan } from '~Shared/Composables/useAuth'

const { invoice, payments, paymentsSummary } = usePage().props
</script>

<template>
    <div
        v-if="authCan('can_view_payments') || invoice.is_draft"
        class="w-full"
    >
        <v-flex
            xs12
            mt-4
        >
            <InvoicePayments
                :invoice="invoice"
                :payments="payments"
                :can-manage-payments="authCan('can_manage_payments')"
                :can-view-payments="authCan('can_view_payments')"
                :payments-summary="paymentsSummary"
            />
        </v-flex>
    </div>
</template>
