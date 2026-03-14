<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router } from '@inertiajs/vue2'

const props = defineProps({
    deduction: {
        type: Object,
        required: true,
    },
    symbol: {
        type: String,
        required: true,
    },
    id: {
        type: Number,
        required: true,
    },
    taxes: {
        type: Array,
        required: false,
        default: () => [],
    },
})

const { $t, $confirm, $route } = useGlobalVariables()

const handleDelete = (id) => {
    $confirm({
        title: $t('labels.delete'),
        question: $t('invoices.message.deduction.confirm-deleting'),
        type: 'delete',
        async confirm() {
            router.delete(
                $route('invoice.deduction.remove.manual', {
                    invoice: props.id,
                    deductionManual: id,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['flash', 'errors', 'deductions', 'invoice'],
                },
            )
        },
    })
}
</script>
<template>
    <tr class="tw-text-sm tw-text-center">
        <td
            colspan="4"
            class="tw-pl-4 tw-text-sm tw-font-bold tw-text-right"
        >
            {{ deduction.name }}
            {{ deduction.operator == '-' ? deduction.operator : '' }}
            {{ deduction.amount
            }}{{ deduction.operator == '-' ? '' : deduction.operator }}
        </td>
        <td>
            -{{
                deduction.operator == '%'
                    ? deduction.deducted_amount.toFixed(2)
                    : deduction.deducted_amount.toFixed(2)
            }}
            {{ symbol }}
        </td>
        <td>
            <v-btn
                v-tooltip="$t('invoices.labels.remove-deduction')"
                small
                flat
                class="tw-my-0"
                icon
                color="error"
                @click="handleDelete(deduction.id)"
            >
                <v-icon small>remove_circle</v-icon>
            </v-btn>
        </td>
    </tr>
</template>
