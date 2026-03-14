<script setup>
import number_format from '@/Utils/NumberFormat'
defineProps({
    deduction: {
        type: Object,
        required: true,
    },
    symbol: {
        type: String,
        required: true,
    },
    taxes: {
        type: Array,
        required: true,
    },
})
</script>
<template>
    <tr class="tw-align-top tw-text-sm tw-text-center item-row no-border">
        <td colspan="9" />
        <td>
            <div class="tw-text-right">
                {{ deduction.name }}
                {{ deduction.operator == '-' ? deduction.operator : '' }}
                {{ deduction.amount
                }}{{ deduction.operator == '-' ? '' : deduction.operator }}
            </div>
        </td>
        <td class="">
            <div class="tw-text-right">
                -{{
                    deduction.operator == '%'
                        ? number_format(deduction.deducted_amount, 2, '.', ',')
                        : number_format(deduction.deducted_amount, 2, '.', ',')
                }}
                {{ symbol }}
            </div>
        </td>
    </tr>
</template>
