<script setup>
import axios from 'axios'
import { toRef } from 'vue'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const props = defineProps({
    tile: {
        type: Boolean,
        default: false,
    },

    color: {
        type: String,
        default: 'grey darken-2',
    },

    invoice: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && typeof value.id === 'number'
        },
    },
})

const { $route } = useGlobalVariables()
const isTile = toRef(props, 'tile')

const extractFilename = (disposition) => {
    if (disposition && disposition.includes('filename=')) {
        const match = disposition.match(/filename="?([^";]+)"?/)
        if (match && match[1]) {
            return match[1]
        }
    }
    return `invoice-${props.invoice}.pdf`
}

const downloadAsPdf = async () => {
    try {
        const response = await axios.get(
            $route('invoicify.pdf.download', { invoice: props.invoice.id }),
            {
                responseType: 'blob',
            },
        )
        const blob = new Blob([response.data], { type: 'application/pdf' })
        const link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        link.download = extractFilename(response.headers['content-disposition'])
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(link.href)
    } catch (error) {
        // Optionally handle error, e.g. show a notification
        console.error('Failed to download PDF', error)
    }
}
</script>

<template>
    <v-list-tile
        v-if="isTile"
        @click="downloadAsPdf"
    >
        <v-list-tile-avatar>
            <v-icon :color="color">file_download</v-icon>
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title>Download</v-list-tile-title>
        </v-list-tile-content>
    </v-list-tile>

    <v-btn
        v-else
        :color="color"
        icon
        flat
        title="Download"
        @click="downloadAsPdf"
    >
        <v-icon>file_download</v-icon>
    </v-btn>
</template>
