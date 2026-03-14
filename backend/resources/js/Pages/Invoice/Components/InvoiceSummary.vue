<script setup>
import { authCan } from '@/Composables/useAuth'
import { useGetPageProps } from '@/Composables/useGetPageProps'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router } from '@inertiajs/vue2'
import { get } from '@vueuse/core'
import { computed, inject } from 'vue'
import { Fragment } from 'vue-frag'
import DeductionRow from './DeductionRow.vue'

const props = defineProps({
    invoiceSummary: {
        required: true,
        type: Object,
    },
    colSpan: {
        type: Number,
        required: false,
        default: 4,
    },
    isLoading: {
        type: Boolean,
        required: false,
        default: true,
    },
})

const { $confirm, $t, $route } = useGlobalVariables()

const canManageTaxes = computed(() => authCan('can_manage_taxes'))
const invoiceIsEditable = computed(() => authCan('invoice_is_editable'))
const deductions = computed(() => get(props.invoiceSummary, 'deductions', []))
const manualDeductions = computed(() =>
    get(props.invoiceSummary, 'manual_deductions', []),
)
const taxes = computed(() => get(props.invoiceSummary, 'taxes', []))

const confirmRemovingTax = inject('confirmRemovingTax')
const addTax = inject('addTax')

function deletingDeduction(invoiceDeduction) {
    const invoice = useGetPageProps('invoice')
    $confirm({
        title: $t('labels.delete'),
        question: $t('invoices.message.deduction.confirm-deleting'),
        type: 'delete',
        async confirm() {
            router.delete(
                $route('invoices.deductions.remove', {
                    invoice,
                    invoiceDeduction,
                }),
                {
                    preserveScroll: true,
                    only: ['invoiceSummary'],
                },
            )
        },
    })
}
</script>
<template>
    <Fragment>
        <tr class="tw-text-sm tw-text-center">
            <td
                :colspan="colSpan"
                class="tw-text-right"
            >
                <strong>
                    {{ $t('invoices.labels.subtotal') }}
                </strong>
            </td>
            <td class="tw-whitespace-nowrap tw-font-semibold">
                {{ invoiceSummary?.subtotal_formatted }}
            </td>
            <!-- v-if="taxEditable" -->
            <td
                v-if="invoiceIsEditable && canManageTaxes"
                class=""
            >
                <v-btn
                    v-tooltip="$t('invoices.labels.add-tax')"
                    small
                    flat
                    class="tw-my-0"
                    icon
                    color="info"
                    @click="addTax"
                >
                    <v-icon small>add_circle_outline</v-icon>
                </v-btn>
            </td>
        </tr>

        <!-- Manual Deductions -->
        <DeductionRow
            v-for="manualDeduction in manualDeductions"
            :key="`manual-deduction-${manualDeduction.id}`"
            :deduction="manualDeduction.deductible"
            :deletable="invoiceIsEditable"
            amount-class="tw-text-nowrap"
            @click:delete="deletingDeduction(manualDeduction)"
        />

        <template v-if="invoiceSummary?.subtotal_after_deduction">
            <tr class="tw-text-sm tw-text-center">
                <td
                    :colspan="colSpan"
                    class="tw-text-right"
                >
                    {{ $t('invoices.labels.net-amount') }}
                </td>
                <td class="tw-whitespace-nowrap">
                    {{ invoiceSummary?.subtotal_after_deduction_formatted }}
                </td>
            </tr>
            <tr
                v-for="(tax, idx) in taxes"
                :key="`subtotal-tax-${idx}`"
                class="tw-text-sm tw-text-center"
            >
                <td
                    :colspan="colSpan"
                    class="tw-text-right"
                >
                    <span class=""> {{ tax.name }} ({{ tax.rate }}%) </span>
                </td>
                <td class="">
                    {{
                        invoiceSummary?.vat_on_subtotal_after_deduction_formatted
                    }}
                </td>
            </tr>
            <tr class="tw-text-sm tw-text-center">
                <td
                    :colspan="colSpan"
                    class="tw-text-right"
                >
                    <strong>
                        {{ $t('invoices.labels.total-amount-incl-vat') }}
                    </strong>
                </td>
                <td class="tw-whitespace-nowrap tw-font-semibold">
                    {{
                        invoiceSummary?.subtotal_after_deduction_with_vat_formatted
                    }}
                </td>
            </tr>
            <tr>
                <td colspan="6" />
            </tr>
        </template>

        <!-- Invoice Deductions -->
        <DeductionRow
            v-for="deduction in deductions"
            :key="`deduction-${deduction.id}`"
            :deduction="deduction"
            :deletable="invoiceIsEditable"
            amount-class="tw-text-nowrap"
        />

        <tr v-if="deductions.length">
            <td colspan="6" />
        </tr>

        <!-- Net Amount -->
        <tr
            v-show="deductions.length || manualDeductions.length"
            class="tw-text-sm tw-text-center"
        >
            <td
                :colspan="colSpan"
                class="tw-text-right"
            >
                <strong>
                    {{ $t('invoices.labels.net-amount') }}
                </strong>
            </td>
            <td class="tw-whitespace-nowrap tw-font-semibold">
                {{ invoiceSummary?.net_amount_formatted }}
            </td>
        </tr>

        <!-- Taxes -->
        <tr
            v-for="(tax, idx) in taxes"
            :key="`tax-${idx}`"
            class="tw-text-sm tw-text-center"
        >
            <td
                :colspan="colSpan"
                class="tw-text-right"
            >
                <span class=""> {{ tax.name }} ({{ tax.rate }}%) </span>
            </td>
            <td class="">{{ tax.amount_formatted }}</td>
            <td v-if="invoiceIsEditable && canManageTaxes">
                <v-btn
                    v-tooltip="$t('invoices.labels.remove-tax')"
                    small
                    flat
                    class="tw-my-0"
                    icon
                    color="error"
                    @click="confirmRemovingTax(tax)"
                >
                    <v-icon small>remove_circle</v-icon>
                </v-btn>
            </td>
        </tr>

        <!-- Grand Total -->
        <tr class="tw-text-sm tw-text-center">
            <td
                :colspan="colSpan"
                class="tw-text-right"
            >
                <strong>
                    {{ $t('invoices.labels.grand-total') }}
                </strong>
            </td>
            <td class="tw-whitespace-nowrap tw-font-semibold">
                {{ invoiceSummary?.grand_total_formatted }}
            </td>
        </tr>
        <!-- TODO: Check components below and deletet if not used! -->
        <!-- <Deductions
            v-for="(deduction, index) in deductions"
            :key="index"
            :deduction="deduction"
            :symbol="currency?.symbol"
        />
        <DeductionManual
            v-for="(deduction, index) in manualDeductions"
            :id="invoice.id"
            :key="index"
            :deduction="deduction"
            :symbol="currency?.symbol"
        /> -->
        <!-- <DeductionTotal
            v-if="hasDeductions"
            :total="totalAfterDeduction"
            :tax-rate="taxRate"
            :tax-name="taxName"
            :tax="taxAfterDeduction"
            :deducted-subtotal="deductedSubtotal"
            :symbol="currency?.symbol"
            :total-discount="totalDiscount"
            :subtotal="subtotal.toString()"
        /> -->
    </Fragment>
</template>
