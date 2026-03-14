<script setup>
import axios from 'axios'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { EventBus } from '~Invoicify/Services/EventBus'
import Events from '~Invoicify/Enums/Events'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'
import Loading from '~Shared/Components/Loading.vue'

/**
 * @typedef {Object} Props
 * @property {import('../types/invoice').Invoice} invoice - The invoice object
 */
const props = defineProps({
    invoice: {
        required: true,
        type: Object,
        validator: (value) => {
            return value && typeof value.id === 'number'
        },
    },
    headers: {
        required: false,
        type: Object,
    },
    zoom: {
        type: Number,
        default: 100,
    },
})

const blobURL = ref(null)
const error = ref(null)
const isLoading = ref(false)
const { $route } = useGlobalVariables()

const streamPdf = async () => {
    try {
        isLoading.value = true
        const response = await axios.get(
            $route('invoicify.pdf.stream', { invoice: props.invoice.id }),
            {
                responseType: 'blob',
                headers: props.headers,
            },
        )
        blobURL.value = URL.createObjectURL(response.data)
    } catch (err) {
        error.value = 'Error loading PDF'
    } finally {
        isLoading.value = false
    }
}

const onInvoiceUpdated = (payload) => {
    if (payload.invoice === props.invoice.id) {
        URL.revokeObjectURL(blobURL.value)
        blobURL.value = null
        streamPdf()
    }
}

onBeforeUnmount(() => {
    if (blobURL.value) {
        URL.revokeObjectURL(blobURL.value)
    }

    EventBus.$off(Events.INVOICE_UPDATED, onInvoiceUpdated)
})

onMounted(async () => {
    await streamPdf()

    EventBus.$on(Events.INVOICE_UPDATED, onInvoiceUpdated)
})
</script>

<template>
    <div
        class="grey darken-4"
        style="
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        "
    >
        <Loading v-if="isLoading" />
        <div
            v-else-if="!isLoading && error"
            class="tw-flex tw-flex-row tw-items-center tw-justify-center"
        >
            <v-icon
                color="red darken-2"
                large
            >
                error
            </v-icon>
            <p class="red--text tw-mb-0 ml-1">
                PDF cannot be loaded. Please try again later.
            </p>
        </div>
        <iframe
            v-if="blobURL"
            :src="`${blobURL}#zoom=${zoom}`"
            type="application/pdf"
            class="flex-grow-1"
            frameborder="0"
        />
    </div>
</template>
<style scoped>
iframe {
    background: transparent;
    min-height: 500px;
    height: 100%;
    width: 100%;
    border: none;
}
</style>
