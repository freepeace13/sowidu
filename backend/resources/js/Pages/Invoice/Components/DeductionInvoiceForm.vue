<script setup>
import { ref, watch } from 'vue'
import DeductionInvoicesOptions from './DeductionInvoicesOptions.vue'

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Object,
        required: true,
    },
    close: {
        type: Function,
        required: true,
    },
    activeTab: {
        type: String,
        required: true,
    },
})
const deductionInvoiceOptionsRef = ref(null)

watch(
    () => props.activeTab,
    (activeTab) => {
        if (activeTab === 'invoice_deduction') {
            deductionInvoiceOptionsRef.value.reset()
        }
    },
)
</script>
<template>
    <DeductionInvoicesOptions
        ref="deductionInvoiceOptionsRef"
        :invoice="invoice"
        :permissions="permissions"
        :close="close"
    />
</template>
