<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { Fragment } from 'vue-frag'
defineProps({
    total: {
        type: String,
        required: true,
    },
    deductedSubtotal: {
        type: String,
        required: true,
    },
    totalDiscount: {
        type: String,
        required: true,
    },
    subtotal: {
        type: String,
        required: true,
    },
    taxRate: {
        type: Number,
        required: true,
    },
    taxName: {
        type: String,
        require: true,
    },
    tax: {
        type: String,
        required: true,
    },
    symbol: {
        type: String,
        required: true,
    },
})
const { $t } = useGlobalVariables()
</script>
<template>
    <Fragment>
        <tr class="tw-text-sm tw-text-center">
            <td
                colspan="4"
                class="tw-pl-4 tw-text-sm tw-font-bold tw-text-right"
            >
                {{ $t('invoices.labels.subtotal') }}
            </td>
            <td class="tw-whitespace-nowrap tw-text-center">
                {{ deductedSubtotal }} {{ symbol }}
            </td>
            <td />
        </tr>
        <tr class="tw-text-sm tw-text-center">
            <td
                colspan="4"
                class="tw-pl-4 tw-text-sm tw-font-bold tw-text-right"
            >
                {{ taxName }}{{ taxRate }}
            </td>
            <td class="tw-text-center tw-pr-8 tw-py-1.5">
                {{ tax }}
                {{ symbol }}
            </td>
            <td />
        </tr>
        <tr class="tw-text-sm tw-text-center">
            <td
                colspan="4"
                class="tw-pl-4 tw-text-sm tw-font-bold tw-text-right tw-underline tw-uppercase"
            >
                Total
            </td>
            <td class="tw-text-center tw-pr-8 tw-whitespace-nowrap font-bold">
                {{ total }}
                {{ symbol }}
            </td>
            <td />
        </tr>
    </Fragment>
</template>
