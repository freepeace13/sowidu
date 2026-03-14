<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'

defineProps({
    deduction: {
        type: Object,
        required: true,
    },
    withAction: {
        required: false,
        type: Boolean,
        default: true,
    },
    colSpan: {
        required: false,
        type: Number,
        default: 4,
    },
    amountClass: {
        required: false,
        type: String,
        default: '',
    },
    deletable: {
        required: false,
        type: Boolean,
        default: false,
    },
})

defineEmits(['click:delete'])

const { $t } = useGlobalVariables()
</script>
<template>
    <tr class="tw-text-sm tw-text-center">
        <td
            :colspan="colSpan"
            class="tw-text-sm tw-text-right"
        >
            {{ deduction.label }}:
        </td>
        <td>
            <div :class="amountClass">- {{ deduction?.amount_formatted }}</div>
        </td>
        <td v-if="withAction && deletable">
            <v-btn
                v-tooltip="$t('invoices.labels.remove-deduction')"
                small
                flat
                class="tw-my-0"
                icon
                color="error"
                @click="$emit('click:delete')"
            >
                <v-icon small>remove_circle</v-icon>
            </v-btn>
        </td>
    </tr>
</template>
