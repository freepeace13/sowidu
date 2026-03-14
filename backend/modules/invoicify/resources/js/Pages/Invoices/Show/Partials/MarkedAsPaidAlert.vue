<script setup>
import { usePage } from '@inertiajs/vue2'

const invoice = usePage().props.invoice
</script>

<template>
    <v-card-title primary-title>
        <v-alert
            :value="invoice.is_paid"
            type="success"
            class="tw-w-full"
            icon="price_check"
        >
            {{ $t('invoices.message.mark-as-paid') }}
        </v-alert>
    </v-card-title>
</template>
