<script setup>
import number_format from '@/Utils/NumberFormat'

defineProps({
    total: {
        type: String,
        required: true,
    },
    subtotal: {
        type: String,
        required: true,
    },
    taxes: {
        type: Array,
        required: true,
    },
    symbol: {
        type: String,
        required: true,
    },
    invoice: {
        type: String,
        required: true,
    },
    paymentDate: {
        type: String,
        required: true,
    },
    visible: {
        type: Boolean,
        required: true,
    },
    totalTax: {
        type: Number,
        required: true,
    },
})
</script>
<template>
    <tbody>
        <tr class="tw-align-top tw-text-sm tw-text-center item-row no-border">
            <td
                colspan="9"
                class="tw-font-bold tw-text-right tw-pt-4"
            >
                {{ invoice }}
            </td>
            <td
                class="tw-capitalize tw-font-bold tw-underline tw-text-right tw-pt-4"
            >
                {{ $t('invoices.labels.subtotal') }}:
            </td>
            <td class="tw-text-right tw-pt-4">
                -{{ number_format(subtotal, 2, '.', ',') }} {{ symbol }}
            </td>
        </tr>
        <tr class="tw-align-top tw-text-sm tw-text-center item-row no-border">
            <td
                colspan="9"
                class="tw-text-right"
            />
            <td class="tw-text-right">
                <ul class="tw-list-none">
                    <li
                        v-for="(tax, index) in taxes"
                        :key="index"
                    >
                        -{{ tax.name }}
                    </li>
                </ul>
            </td>
            <td class="tw-text-right">- {{ totalTax }} {{ symbol }}</td>
        </tr>
        <tr class="tw-align-top tw-text-sm tw-text-center item-row no-border">
            <td
                colspan="9"
                class="tw-pb-2"
            />
            <td
                class="tw-capitalize tw-font-bold tw-underline tw-text-right pb-2"
            >
                {{ $t('invoices.labels.total') }}:
            </td>
            <td
                class="tw-capitalize tw-font-bold tw-underline tw-text-right pb-2"
            >
                -{{ total }} {{ symbol }}
            </td>
        </tr>
    </tbody>
</template>
