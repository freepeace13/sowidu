// TODO - delete this file
<script setup>
import { useGetPageProps } from '@/Composables/useGetPageProps'
import { router } from '@inertiajs/vue2'
import { computed, ref } from 'vue'
import DeductionRow from './../DeductionRow.vue'
import { useEventBus } from '@vueuse/core'

defineProps({
    invoice: {
        type: Object,
        required: true,
    },
})

const isFetching = ref(true)
const invoiceSummaryLoadingEmitter = useEventBus(
    'invoice.preview.summary.finished',
)

const invoiceSummary = computed(() =>
    useGetPageProps('invoiceSummary', {
        deductions: [],
        taxes: [],
    }),
)

const taxes = computed(() => invoiceSummary.value?.taxes ?? [])
const deductions = computed(() => invoiceSummary.value?.deductions ?? [])
const manualDeductions = computed(
    () => invoiceSummary.value?.manual_deductions ?? [],
)

router.reload({
    preserveState: true,
    preserveScroll: true,
    only: ['invoiceSummary'],
    onBefore: () => {
        isFetching.value = true
    },
    onFinish: () => {
        isFetching.value = false
        invoiceSummaryLoadingEmitter.emit('invoice.preview.summary.finished')
    },
})
</script>
<template>
    <table class="w-full tw-mt-5">
        <tbody v-show="!isFetching">
            <tr
                class="tw-align-top tw-text-sm tw-text-center item-row no-border"
            >
                <td />
                <td class="tw-w-[125px]">
                    <div class="tw-text-right">
                        {{ $t('invoices.labels.subtotal') }}:
                    </div>
                </td>
                <td class="tw-w-[125px]">
                    <div class="tw-text-right">
                        {{ invoiceSummary?.subtotal_formatted }}
                    </div>
                </td>
            </tr>
            <tr />
            <!-- Manual Deductions -->
            <DeductionRow
                v-for="deduction in manualDeductions"
                :key="`manual-deduction-${deduction.id}`"
                :deduction="deduction"
                :with-action="false"
                amount-class="tw-text-right"
                :col-span="2"
            />
            <tr
                v-if="deductions.length && manualDeductions.length"
                style="height: 1rem"
            />
            <tr
                v-if="deductions.length && manualDeductions.length"
                class="tw-align-top tw-text-sm tw-text-center item-row no-border"
            >
                <td />
                <td class="tw-text-right">
                    {{ $t('invoices.labels.net-amount') }}:
                </td>
                <td class="tw-whitespace-nowrap tw-text-right">
                    {{ invoiceSummary.net_manual_amount }}
                </td>
            </tr>
            <!-- Taxes -->
            <tr
                v-for="(tax, idx) in taxes"
                v-show="deductions.length && manualDeductions.length"
                :key="`tax-${idx}`"
                class="tw-align-top tw-text-sm tw-text-center item-row no-border"
            >
                <td
                    colspan="2"
                    class="tw-text-right"
                >
                    {{ tax.name }} {{ tax.rate }}%:
                </td>
                <td class="tw-whitespace-nowrap tw-text-right">
                    {{ invoiceSummary.net_manual_tax }}
                </td>
            </tr>

            <!-- Grand Total -->
            <tr
                v-if="deductions.length && manualDeductions.length"
                class="tw-align-top tw-text-sm tw-text-center item-row no-border"
            >
                <td />

                <td class="tw-text-right">
                    {{ $t('invoices.labels.grand-total') }}:
                </td>
                <td class="tw-whitespace-nowrap tw-text-right">
                    {{ invoiceSummary?.net_manual_grand_total }}
                </td>
            </tr>

            <tr v-if="deductions.length && manualDeductions.length">
                <td />
                <td
                    colspan="2"
                    class="py-2 single-border"
                />
            </tr>

            <!-- Invoice Deductions -->
            <template v-for="deduction in deductions">
                <tr :key="`deduction-row-top-spacer-${deduction.id}`">
                    <td class="py-2" />
                </tr>
                <tr
                    :key="`deduction-row-${deduction.id}`"
                    class="tw-text-sm tw-text-center"
                >
                    <td class="tw-text-sm tw-text-right">
                        {{ deduction.label }}
                    </td>
                    <td class="tw-text-right">
                        {{ $t('invoices.labels.subtotal') }}:
                    </td>
                    <td>
                        <div class="tw-text-right">
                            - {{ deduction?.amount_formatted }}
                        </div>
                    </td>
                </tr>
                <tr
                    v-for="tax in deduction?.taxes ?? []"
                    :key="`invoice-${deduction.id}-deduction-tax-${tax.id}`"
                    class="tw-text-sm"
                >
                    <td />
                    <td class="tw-text-right">{{ tax.name }}:</td>
                    <td class="tw-text-right">- {{ tax.amount_formatted }}</td>
                </tr>
                <tr
                    :key="`deduction-grand-total-${deduction.id}`"
                    class="tw-align-top tw-text-sm tw-text-center item-row no-border"
                >
                    <td />
                    <td class="tw-text-right grand-total">
                        {{ $t('invoices.labels.grand-total') }}:
                    </td>
                    <td class="tw-whitespace-nowrap tw-text-right grand-total">
                        - {{ deduction.grand_total_formatted }}
                    </td>
                </tr>
                <tr :key="`deduction-row-bottom-spacer-${deduction.id}`">
                    <td />
                    <td
                        colspan="2"
                        class="py-2 single-border"
                    />
                </tr>
            </template>

            <!-- Net Amount -->
            <tr
                v-if="deductions.length || manualDeductions.length"
                style="height: 1rem"
            />
            <tr
                v-if="deductions.length || manualDeductions.length"
                class="tw-align-top tw-text-sm tw-text-center item-row no-border tw-font-bold"
            >
                <td />
                <td class="tw-text-right">
                    <strong> {{ $t('invoices.labels.net-amount') }}: </strong>
                </td>
                <td class="tw-whitespace-nowrap tw-font-semibold tw-text-right">
                    {{ invoiceSummary?.net_amount_formatted }}
                </td>
            </tr>

            <!-- Taxes -->
            <tr
                v-for="(tax, idx) in taxes"
                :key="`tax-${tax.id}-${idx}`"
                class="tw-align-top tw-text-sm tw-text-center item-row no-border tw-font-semibold"
            >
                <td
                    colspan="2"
                    class="tw-text-right"
                >
                    {{ tax.name }} {{ tax.rate }}%:
                </td>
                <td class="tw-whitespace-nowrap tw-text-right">
                    {{ tax.amount_formatted }}
                </td>
            </tr>

            <!-- Grand Total -->
            <tr
                class="tw-align-top tw-text-sm tw-text-center item-row no-border tw-font-bold"
            >
                <td />

                <td class="tw-text-right">
                    <strong> {{ $t('invoices.labels.grand-total') }}: </strong>
                </td>
                <td class="tw-whitespace-nowrap tw-font-semibold tw-text-right">
                    {{ invoiceSummary?.grand_total_formatted }}
                </td>
            </tr>
            <tr>
                <td />
                <td
                    colspan="2"
                    class="py-2 double-underline"
                />
            </tr>
        </tbody>
        <tfoot>
            <tr
                v-for="loader in 3"
                v-show="isFetching"
                :key="`loader-${loader}`"
            >
                <td class="tw-whitespace-nowrap tw-text-right pr-4">
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        size="20"
                    />
                </td>
            </tr>
        </tfoot>
    </table>
</template>
<style scoped>
.double-underline {
    border-top: 3px double #000;
}

.single-border {
    border-top: 1px solid #000;
}
</style>
