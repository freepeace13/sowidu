<script setup>
import { authCan } from '@/Composables/useAuth'

defineProps({
    invoice: {
        type: Object,
        required: true,
    },
    isDownloading: {
        type: Boolean,
        required: true,
    },
    print: {
        type: Function,
        required: true,
    },
    confirmSendToClient: {
        type: Function,
        required: true,
    },
    confirmMarkingAsPaid: {
        type: Function,
        required: true,
    },
    refreshLayout: {
        type: Function,
        required: true,
    },
    enableDownloading: {
        required: false,
        type: Boolean,
        default: true,
    },
})

defineEmits(['click:download', 'click:add-manual-item'])
</script>
<template>
    <v-toolbar
        class="no-print"
        color="white"
        flat
    >
        <v-toolbar-title class="title tw-flex tw-items-center">
            <div class="md:tw-text-xl tw-text-lg">
                {{ $t('invoices.labels.print-preview') }}
            </div>
        </v-toolbar-title>
        <v-spacer />
        <v-btn
            v-if="authCan('can_update_invoice')"
            color="secondary"
            depressed
            @click="refreshLayout"
        >
            <v-icon left>restart_alt</v-icon>
            {{ $t('invoices.buttons.reset-layout') }}
        </v-btn>
        <v-btn
            v-if="invoice.status.is_draft && authCan('can_update_invoice')"
            color="purple"
            class="white--text"
            :disabled="!invoice.status.is_draft"
            depressed
            @click="$emit('click:add-manual-item')"
        >
            <v-icon left>draw</v-icon>
            {{ $t('invoices.buttons.add-manual-item') }}
        </v-btn>
        <v-btn
            v-if="invoice.status.is_draft && authCan('can_send_invoice')"
            color="info"
            :disabled="!invoice.status.is_draft"
            depressed
            @click="confirmSendToClient"
        >
            <v-icon left>email</v-icon>
            {{ $t('invoices.buttons.send-to-client') }}
        </v-btn>
        <v-btn
            v-if="invoice.is_paid"
            color="success"
            depressed
        >
            <v-icon left>done_outline</v-icon>
            {{ $t('invoices.labels.paid') }}
        </v-btn>
        <v-btn
            v-if="!invoice.is_paid && authCan('can_mark_as_paid')"
            color="primary"
            :disabled="!invoice.status.is_pending"
            @click="confirmMarkingAsPaid"
        >
            <v-icon left>email</v-icon>
            {{ $t('invoices.buttons.mark-as-paid') }}
        </v-btn>
        <v-btn
            id="download-pdf-btn"
            color="primary"
            class="download-pdf-btn-class"
            depressed
            :loading="isDownloading"
            :disabled="isDownloading || !enableDownloading"
            @click="$emit('click:download')"
        >
            <v-icon left>file_download</v-icon>
            {{ $t('buttons.download-pdf') }}
        </v-btn>
        <!-- <v-btn
            color="primary"
            depressed
            :disabled="true"
            @click="print"
        >
            <v-icon left>print</v-icon>
            {{ $t('invoices.buttons.print') }}
        </v-btn> -->
    </v-toolbar>
</template>
