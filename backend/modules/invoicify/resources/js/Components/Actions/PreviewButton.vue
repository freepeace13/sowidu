<script setup>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue2'
import PreviewDialog from '~Invoicify/Components/PreviewDialog.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import axios from 'axios'
import { EventBus } from '~Invoicify/Services/EventBus'

const { $t, $route } = useGlobalVariables()
const page = usePage()
const dialogKey = ref(0)
const props = defineProps({ label: String })
const invoice = usePage().props.invoice
const shown = ref(false)
const isLoading = ref(false)
const label = computed(() => props.label || $t('invoices.buttons.preview'))

const openPreview = async () => {
    isLoading.value = true
    try {
        const res = await axios.get(
            $route('invoicify.pdf.stream', { invoice: invoice.id }),
            { responseType: 'blob' },
        )
        if (res.data.size > 0) {
            shown.value = true
        } else {
            EventBus.$emit('flash.error', 'PDF cannot be loaded')
        }
    } catch (err) {
        let msg = 'PDF cannot be loaded'
        if (err.response?.data instanceof Blob) {
            try {
                const data = JSON.parse(await err.response.data.text())
                msg = data.errors
                    ? Object.values(data.errors).flat().join('<br/>')
                    : data.message || msg
            } catch {
                //
            }
        }
        EventBus.$emit('flash.error', msg)
    } finally {
        isLoading.value = false
    }
    shown.value = true
}

const reloadPreviewDialog = () => {
    dialogKey.value++
    shown.value = true
}

watch(
    () => page.props.flash?.preview_open,
    (val) => {
        if (val) {
            reloadPreviewDialog()
        }
    },
    { immediate: true },
)
</script>

<template>
    <div>
        <v-btn
            color="grey-lighter"
            depressed
            @click="openPreview"
        >
            <v-icon left>visibility</v-icon>
            {{ label }}
        </v-btn>

        <portal to="preview-dialog">
            <PreviewDialog
                :key="dialogKey"
                v-model="shown"
                :invoice="invoice"
            />
        </portal>
    </div>
</template>
