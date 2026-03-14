<script setup>
import { router } from '@inertiajs/vue2'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const props = defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
    invoiceIds: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
})

const { $t, $route } = useGlobalVariables()

function handleBulkExport() {
    router.post(
        $route('invoicify.bulk-export'),
        {
            invoice_ids: props.invoiceIds,
            filters: props.filters,
        },
        // {
        //     onStart: () => {
        //         isBulkExporting.value = true
        //     },
        //     onFinish: () => {
        //         isBulkExporting.value = false
        //     },
        // },
    )
}
</script>

<template>
    <v-btn
        :disabled="disabled"
        block
        large
        color="primary"
        depressed
        class="white--text xs:!tw-my-2 xs:!tw-h-14 mx-2 xs:!tw-mx-0"
        @click="handleBulkExport"
    >
        <v-icon v-if="$vuetify.breakpoint.smAndDown"> file_download </v-icon>
        <span v-if="$vuetify.breakpoint.smAndUp">
            {{ $t('buttons.export') }}
        </span>
    </v-btn>
</template>
