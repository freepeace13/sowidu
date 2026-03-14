<script setup>
import CopyLinkButton from './Actions/CopyLinkButton.vue'
import DownloadAsPdfButton from './Actions/DownloadAsPdfButton.vue'
import PdfView from './PdfView.vue'
import PreviewActionButtons from './PreviewActionButtons.vue'
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        default: 'PDF Preview',
    },

    value: {
        type: Boolean,
        default: false,
    },

    invoice: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['input'])

function closeDialog() {
    emit('input', false)
}

const dialogModel = computed({
    get: () => props.value,
    set: (newValue) => {
        emit('input', newValue)
    },
})
</script>

<template>
    <v-dialog
        v-model="dialogModel"
        transition="dialog-bottom-transition"
        hide-overlay
        fullscreen
        lazy
    >
        <v-card style="display: flex; flex-direction: column; height: 100%">
            <v-toolbar
                color="transparent"
                outlined
                flat
            >
                <v-btn
                    icon
                    @click="closeDialog"
                >
                    <v-icon color="grey darken-2">close</v-icon>
                </v-btn>
                <v-toolbar-title>{{ props.title }}</v-toolbar-title>
                <v-spacer />
                <DownloadAsPdfButton :invoice="props.invoice" />
                <CopyLinkButton />
                <PreviewActionButtons :invoice="props.invoice" />
            </v-toolbar>
            <v-divider />
            <div style="min-height: 0; flex: 1 1 0">
                <PdfView :invoice="props.invoice" />
            </div>
        </v-card>
    </v-dialog>
</template>
