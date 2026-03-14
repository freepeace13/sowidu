<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'

defineProps({
    total: {
        type: String,
        required: true,
    },
    subtotal: {
        type: [String, Number],
        required: true,
        default: '0',
    },
    taxRate: {
        type: Number,
        required: true,
    },
    taxName: {
        type: String,
        require: true,
    },
    totalDiscount: {
        type: String,
    },
    tax: {
        type: String,
        required: true,
    },
    deductedSubtotal: {
        type: String,
    },
    symbol: {
        type: String,
        required: true,
    },
    visible: {
        type: Boolean,
    },
})

const { $t } = useGlobalVariables()
</script>
<template>
    <tbody v-if="visible">
        <tr
            class="tw-align-top tw-text-sm tw-text-center item-row no-border tw-underline tw-font-bold"
        >
            <td
                colspan="9"
                class="tw-text-left"
            />
            <td class="tw-pt-4">
                <div class="tw-text-right">
                    {{ $t('invoices.labels.subtotal') }}:
                </div>
            </td>
            <td class="tw-pt-4">
                <div class="tw-text-right">
                    {{ deductedSubtotal }} {{ symbol }}
                </div>
            </td>
        </tr>
        <tr class="item-row no-border tw-text-sm">
            <td colspan="9" />
            <td>
                <div class="tw-text-right">{{ taxName }}:</div>
            </td>
            <td class="!tw-text-right">
                <div class="tw-text-right">
                    {{ tax }}
                    {{ symbol }}
                </div>
            </td>
        </tr>
        <tr
            class="tw-align-top tw-text-sm tw-text-center item-row no-border tw-underline tw-font-bold"
        >
            <td colspan="9" />
            <td class="tw-capitalize tw-font-bold tw-underline">
                <div class="tw-text-right">
                    {{ $t('invoices.labels.total') }}:
                </div>
            </td>

            <td>
                <div class="tw-font-bold tw-underline tw-text-right no-border">
                    {{ total }}
                    {{ symbol }}
                </div>
            </td>
        </tr>
    </tbody>
</template>
